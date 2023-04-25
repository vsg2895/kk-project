<?php namespace Admin\Http\Controllers;

use Admin\Http\Requests\DeleteOrderItemRequest;
use Admin\Http\Requests\UpdateOrderRequest;
use Complex\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Jakten\Facades\Auth;
use Jakten\Models\Order;
use Jakten\Models\OrderItem;
use Jakten\Repositories\Contracts\OrderItemRepositoryContract;
use Jakten\Repositories\Contracts\OrderRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\OrderService;
use Jakten\Services\Payment\Klarna\KlarnaService;
use Shared\Http\Controllers\Controller;

/**
 * Class OrderController
 * @package Admin\Http\Controllers
 */
class OrderController extends Controller
{
    /**
     * @var OrderRepositoryContract
     */
    private $orders;

    /**
     * @var OrderItemRepositoryContract
     */
    private $orderItem;

    /**
     * @var OrderService
     */
    private $orderService;

    /** @var KlarnaService */
    private $klarnaService;

    /**
     * OrderController constructor.
     *
     * @param OrderRepositoryContract $orders
     * @param KKJTelegramBotService $botService
     */
    public function __construct(OrderRepositoryContract $orders, OrderItemRepositoryContract $orderItem, KlarnaService $klarnaService, KKJTelegramBotService $botService, OrderService $orderService)
    {
        parent::__construct($botService);
        $this->orders = $orders;
        $this->orderItem = $orderItem;
        $this->orderService = $orderService;
        $this->klarnaService = $klarnaService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $orders = $this->orders
            ->search($request)
            ->paginate()
            ->appends(Input::except('page'));
        return view('admin::orders.index', [
            'orders' => $orders,
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $order = $this->orders->query()->findOrFail($id);
        return view('admin::orders.show', [
            'order' => $order,
            'user' => Auth::user(),
        ]);
    }

    public function edit($id)
    {
        $order = $this->orders->query()->findOrFail($id);
        return view('admin::orders.edit', [
            'order' => $order,
            'user' => Auth::user(),
        ]);
    }

    public function update($id, UpdateOrderRequest $request)
    {
        $order = $this->orders->query()->findOrFail($id);
        try {
            $this->orderService->updateOrderDetails($order, $request->all());
            return redirect()->back()->withMessage('Order details updated successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function invoiceSentMany(Request $request)
    {
        $orderIds = $request->get('order_id');
        if (count($orderIds)) {
            $orders = $this->orders->query()->whereIn('id', $orderIds)->get();
            foreach ($orders as $order) {
                $order->invoice_sent = true;
                $order->save();
            }
        }

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function transmission(Request $request)
    {
        $orderId = $request->get('order_id');
        $transmission = $request->get('transmission');

        if ($orderId && $transmission) {
            $orderItems = $this->orderItem->query()->where('order_id', '=', (int)$orderId)->get();
            foreach ($orderItems as $item) {
                $item->participant->transmission = $transmission;

                $item->participant->save();
            }
        }


        return redirect()->back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \KlarnaException
     */
    public function cancel(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()) {

            $request->session()->flash('message', 'Annullering misslyckas');
        }

        /** @var Order $order */
        $order = Order::query()->where('id', $id)->first();

        if (!$order) {
            $request->session()->flash('message', 'Ingen sådan order, misslyckas');
            return redirect()->back();
        }

        try {
            $this->klarnaService->refund($order, $order->items());
            $request->session()->flash('message', 'Annullering upp framgång!');
        } catch (Exception $exception) {
            $request->session()->flash('message', 'Annullering misslyckas');
        }

        $request->session()->flash('message', 'Annullering upp framgång!');
        $this->orderService->cancelOrder($order);

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \KlarnaException
     */
    public function rebook(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()) {

            $request->session()->flash('message', 'Annullering misslyckas');
        }

        /** @var Order $order */
        $order = Order::query()->where('id', $id)->first();

        if (!$order) {
            $request->session()->flash('message', 'Ingen sådan order, misslyckas');
            return redirect()->back();
        }

        try {

            $this->orderService->rebookOrder($order);

        } catch (Exception $exception) {
            $request->session()->flash('message', 'Annullering misslyckas');
        }

        $request->session()->flash('message', 'Annullering upp framgång!');

        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function invoiceSent($id)
    {
        $order = $this->orders->query()->findOrFail($id);
        $order->invoice_sent = true;
        $order->save();

        return redirect()->route('admin::orders.show', ['id' => $id]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function deleteOrderDetails(DeleteOrderItemRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $deleteRelationType = $this->orderService->deleteOrderItemWithParticipant($request->item_id);
            return redirect()->back()->withMessage("Order Item $deleteRelationType deleted successfully");
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }
}
