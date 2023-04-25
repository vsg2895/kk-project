<?php namespace Api\Http\Controllers;

use Api\Http\Requests\StoreOrderRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jakten\Events\{NewOrder, NewRegistration};
use Jakten\Facades\Auth;
use Jakten\Helpers\Payment;
use Jakten\Helpers\Roles;
use Jakten\Models\Course;
use Jakten\Models\Order;
use Jakten\Repositories\Contracts\{CourseRepositoryContract, OrderRepositoryContract};
use Jakten\Services\{KKJTelegramBotService,
    OrderService,
    Payment\Klarna\KlarnaService,
    Payment\PaymentService,
    UserService};
use Organization\Http\Requests\UpdateOrderRequest;

/**
 * Class OrderController
 * @package Api\Http\Controllers
 */
class OrderController extends ApiController
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * @var PaymentService
     */
    private $paymentService;

    /**
     * @var CourseRepositoryContract
     */
    private $courses;

    /**
     * @var OrderRepositoryContract
     */
    private $orders;

    /**
     * BookingsController constructor.
     *
     * @param UserService $userService
     * @param OrderService $orderService
     * @param PaymentService $paymentService
     * @param CourseRepositoryContract $courses
     * @param OrderRepositoryContract $orders
     * @param KKJTelegramBotService $botService
     */
    public function __construct(UserService $userService, OrderService $orderService, PaymentService $paymentService, CourseRepositoryContract $courses, OrderRepositoryContract $orders, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->userService = $userService;
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
        $this->courses = $courses;
        $this->orders = $orders;
    }

    /**
     * @param $courseIds
     * @param StoreOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrder($courseIds, StoreOrderRequest $request)
    {
        $courseIds = explode(',', $courseIds); // can be one or several comma seprated ids
        $courses = Course::whereIn('id', $courseIds)->get();

        try {
            $user = Auth::user();
            $existingUser = $user ? true : false;
            $data = DB::transaction(function () use ($request, $courses, $user) {

                if (!$user) {
                    $user = $this->userService->storeUser(with(new FormRequest(
                            array_merge(
                                $request->input('new_user'),
                                ['role_id' => Roles::ROLE_STUDENT]
                            )))
                    );
                }

                $order = $this->orderService->storeOrder($request, $courses, $user);
                $paymentStrategy = $this->paymentService->getPaymentStrategy($request->input('payment_method'));
                $paymentStrategy->pay($order);

                return ['order' => $order, 'user' => $user];
            });
            $order = $data['order'];
            $user = $data['user'];

            Log::info("(event) Raise new event", [
                "class" => __CLASS__,
                "event" => "NewOrder",
                "order" => ["id" => $order->id, "email" => $order->user->email]
            ]);
            event(new NewOrder($order));

            if (!$existingUser) {
                Log::info("(event) Raise new event", [
                    "class" => __CLASS__,
                    "event" => "NewRegistration",
                    "user" => ["id" => $user->id, "email" => $user->email],
                    "newOrganization" => true
                ]);
                event(new NewRegistration($user));
            }

            return $this->success($order);
        } catch (\Exception $e) {
            return $this->error(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function find($id)
    {
        $order = $this->orders->query()->with('user', 'items.participant', 'items.course')->findOrFail($id);
        $this->authorize('view', $order);

        return $this->success($order);
    }

    /**
     * @param $id
     * @param UpdateOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update($id, UpdateOrderRequest $request)
    {
        $order = $this->orders->query()->findOrFail($id);
        $this->authorize('update', $order);

        try {
            $order = $this->orderService->updateOrder($order, $request);
            $order->load(['items.participant', 'user']);

            return $this->success($order);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            $statusCode = 400;

            return $this->error(compact('order', 'message', 'statusCode'));
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancel($id)
    {
        $this->botService->log('full_order_cancellation_triggered');

        /** @var Order $order */
        $order = $this->orders
            ->query()
            ->with('items.participant', 'user')
            ->findOrFail($id);

        $this->botService->log('full_order_cancellation_order_selected', ['order_id' => $order->id]);

        $this->authorize('update', $order);

        $this->botService->log('full_order_cancellation_order_authorized', ['order_id' => $order->id]);

        try {
            $status = false;
            /** @var KlarnaService $klarna */
            $klarna = resolve(KlarnaService::class);

            $this->botService->log('full_order_cancellation', ['order_id' => $order->id]);

            switch ($order->canBeCancelled()) {
                case true:
                    switch ($order->payment_method) {
                        case Payment::KLARNA:
                            switch ($klarna->cancelOrder($order)) {
                                case true :
                                    $this->orderService->cancelOrder($order);
                                    $this->botService->log('full_order_cancellation_success', ['order_id' => $order->id]);
                                    return $this->success(compact('order'));
                                    break;
                                case false:
                                    $this->botService->log('full_order_cancellation_failed', ['order_id' => $order->id, 'message' => 'Failed to cancel an order (using the reservation) via Klarna']);
                                    $message = "Klarna har misslyckats med att avbryta din beställning.\r\n" .
                                        "Kontakta vårt supportteam för att hjälpa dig lösa problemet.";
                                    return $this->error(compact('order', 'status', 'message'));
                                    break;
                            }
                            break;
                        default:
                            $this->orderService->cancelOrder($order);
                            $this->botService->log('full_order_cancellation_success', ['order_id' => $order->id]);
                            return $this->success(compact('order'));
                            break;
                    }
                    break;
                default:
                    $message = 'Beställningen kan inte avbrytas';
                    $this->botService->log('full_order_cancellation_failed', ['order_id' => $order->id, 'message' => 'Order is non-cancellable now']);
                    return $this->error(compact('order', 'status', 'message'));
                    break;
            }
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            Log::info($exception);
            $this->botService->log('full_order_cancellation_failed', ['order_id' => $order->id, 'message' => "Exception fault"]);
            return $this->error(compact('order', 'status', 'message'));
        }
    }

    /**
     * @param Request $request
     * @param $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function activateOrCancel(Request $request, $order)
    {
        try {
            /** @var Order $order */
            $order = Order::where('id', (int)$order)->first();

            $activate = $order->items()
                ->where('delivered', false)
                ->where('credited', false)
                ->where('cancelled', false)
                ->whereIn('id', $request->post('activate', []))
                ->get();

            $cancel = $order->items()
                ->where('delivered', true)
                ->whereNotNull('external_invoice_id')
                ->whereIn('id', $request->post('cancel', []))
                ->get();

            /** @var KlarnaService $klarna */
            $klarna = resolve(KlarnaService::class);

            if ($activate->count()) {
                /** Activation / Delivering */
                $this->botService->log('manual_order_item_activation', ['order_id' => $order->id, 'order_items' => $activate->pluck('id')->toArray()]);

                if ($order->isKlarnaV3()) {
                    $klarna->captureOrderItems($order, $activate);
                } else {
                    $klarna->activateItems($order, $activate);
                }
            }

            if ($cancel->count()) {
                /** Cancelling / Refund */
                $this->botService->log('manual_order_item_refund', ['order_id' => $order->id, 'order_items' => $cancel->pluck('id')->toArray()]);

                if ($order->isKlarnaV3()) {
                    $klarna->cancelOrder($order);
                } else {
                    $klarna->refund($order, $cancel);
                }
            }

            $order->update($this->getHandledAndCancelledState($order));
            $order->refresh();

            $this->botService->log('action_success', true);
            return $this->success(['order' => $order]);
        } catch (\Exception $exception) {
            $this->botService->log('action_success', false);
            Log::info($exception);
            $order->refresh();

            return $this->error(['status' => true, 'message' => $exception->getMessage(), 'order' => $order]);
        }
    }

    /**
     * @param Order $order
     * @return array
     */
    private function getHandledAndCancelledState(Order $order)
    {
        return [
            'handled' => $order->items()->count() === $order->items()->where('delivered', true)->count(),
            'cancelled' => $order->items()->count() === $order->items()->where('cancelled', true)->count()
        ];
    }
}
