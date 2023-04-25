<?php namespace Organization\Http\Controllers;

use Illuminate\Http\Request;
use Jakten\Facades\Auth;
use Jakten\Models\Order;
use Jakten\Repositories\Contracts\OrderRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\OrderService;
use Shared\Http\Controllers\Controller;

/**
 * Class OrderController
 * @package Organization\Http\Controllers
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
        $user = Auth::user();
        $orders = $this->orders->forOrganization($user->organization)->query()
            ->with('school')
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('organization::orders.index', [
            'orders' => $orders,
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        return view('organization::orders.show', [
            'orderId' => $id,
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \KlarnaException
     */
    public function update($id, Request $request)
    {
        /** @var Order $order */
        $order = $this->orders->query()->findOrFail($id);
        $this->authorize('update', $order);
        $this->orderService->setDeliveredItems($order, $request->input('delivered', []));

        return redirect()->route('organization::orders.index');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancel($id)
    {
        /** @var Order $order */
        $order = $this->orders->query()
            ->where('id', $id)
            ->get()
            ->first();

        $this->authorize('update', $order);
        $this->orderService->cancelOrder($order);

        return redirect()->route('organization::orders.show', ['id' => $id]);
    }
}
