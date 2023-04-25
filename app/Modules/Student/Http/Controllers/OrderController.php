<?php namespace Student\Http\Controllers;

use Complex\Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Jakten\Facades\Auth;
use Jakten\Helpers\Payment;
use Jakten\Models\Order;
use Jakten\Repositories\Contracts\OrderRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\OrderService;
use Jakten\Services\Payment\Klarna\KlarnaService;
use Shared\Http\Controllers\Controller;

/**
 * Class OrderController
 * @package Student\Http\Controllers
 */
class OrderController extends Controller
{
    /**
     * @var OrderRepositoryContract
     */
    private $orders;

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * OrderController constructor.
     *
     * @param OrderRepositoryContract $orders
     * @param OrderService $orderService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(OrderRepositoryContract $orders, OrderService $orderService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->orders = $orders;
        $this->orderService = $orderService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $orders = $this->orders->byUser(Auth::user())->query()->orderBy('created_at', 'desc')->get();

        return view('student::orders.index', [
            'orders' => $orders,
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        $order = $this->orders->query()->with('school.city', 'items.course')->findOrFail($id);
        $this->authorize('view', $order);

        return view('student::orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \KlarnaException
     */
    public function cancel($id)
    {
        $order = $this->orders->query()->findOrFail($id);
        $this->authorize('update', $order);

        /** @var KlarnaService $klarna */
        $klarna = resolve(KlarnaService::class);

        $this->botService->log("order_{$order->id}_cancellation", ['order_id' => $order->id]);
        try {
            if ($order->payment_method == Payment::KLARNA && $klarna->cancelOrder($order)) {
                $this->orderService->cancelOrder($order);
                if ($order->cancelled) {
                    $this->botService->log(
                        "order_{$order->id}_cancellation",
                        [
                            'order_id' => $order->id,
                            'message' => 'Success'
                        ]);
                } else {
                    $this->botService->log(
                        "order_{$order->id}_cancellation",
                        [
                            'order_id' => $order->id,
                            'message' => 'Internal service error.'
                        ]);
                }
            } else {
                $this->botService->log(
                    "order_{$order->id}_cancellation",
                    [
                        'order_id' => $order->id,
                        'message' => 'Failed to cancel an order (using the reservation) via Klarna'
                    ]);
            }
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            Log::info($exception);
            $this->botService->log('full_order_cancellation_failed', ['order_id' => $order->id, 'message' => "Exception fault"]);
            return $this->error(compact('order', 'status', 'message'));
        }

        return redirect()->route('student::orders.index')->with(
            'message', $order->cancelled ? 'Beställningen har avbokats.' : 'Beställningen har inte avbrutits.'
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \KlarnaException
     */
    public function rebook($id)
    {
        /** @var Order $order */
        $order = $this->orders->query()->findOrFail($id);
        $this->authorize('update', $order);

        $this->botService->log("order_{$order->id}_rebooking", ['order_id' => $order->id]);

        try {
            $this->orderService->rebookOrder($order);

            $this->botService->log(
                "order_{$order->id}_rebooking",
                [
                    'order_id' => $order->id,
                    'message' => 'Success rebooking an order (using the reservation) via Klarna'
                ]);

        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            Log::info($exception);
            $this->botService->log('order_rebook_failed', ['order_id' => $order->id, 'message' => "Exception fault"]);
            return redirect()->route('student::orders.index')->with(
                'message', 'Beställningen har inte avbrutits.'
            );
        }

        return redirect()->route('student::orders.index')->with(
            'message', $order->cancelled ? 'Din kurs är avbokad och du har nu ett saldo på ditt konto hos som du kan använda för att boka ett nytt kurstillfälle. Du måste vara inloggad för att göra din nya bokning.' : 'Beställningen har inte avbrutits.'
        );
    }
}
