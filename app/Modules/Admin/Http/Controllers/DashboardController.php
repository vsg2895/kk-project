<?php namespace Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Jakten\Models\Order;
use Jakten\Models\OrderItem;
use Jakten\Repositories\Contracts\OrderItemRepositoryContract;
use Jakten\Repositories\Contracts\OrderRepositoryContract;
use Jakten\Repositories\Contracts\SchoolClaimRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Shared\Http\Controllers\Controller;

/**
 * Class DashboardController
 * @package Admin\Http\Controllers
 */
class DashboardController extends Controller
{
    /**
     * @var OrderItemRepositoryContract
     */
    private $orderItems;

    /**
     * @var OrderItemRepositoryContract
     */
    private $orders;

    /**
     * DashboardController constructor.
     *
     * @param OrderItemRepositoryContract $orderItems
     * @param OrderRepositoryContract $orders
     * @param SchoolClaimRepositoryContract $claims
     * @param KKJTelegramBotService $botService
     */
    public function __construct(OrderItemRepositoryContract $orderItems, OrderRepositoryContract $orders, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->orderItems = $orderItems;
        $this->orders = $orders;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard(Request $request)
    {
        $startTime = $request->start_time ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $endTime = $request->end_time ?: Carbon::now()->format('Y-m-d');

        $prevStartTime = $request->start_time ? Carbon::createFromFormat('Y-m-d', $request->start_time)->subYear()->format('Y-m-d')
            : Carbon::now()->startOfMonth()->subYear()->format('Y-m-d');

        $prevEndTime = $request->end_time ? Carbon::createFromFormat('Y-m-d', $request->end_time)->subYear()->format('Y-m-d')
            : Carbon::now()->subYear()->format('Y-m-d');

        $filterData['current_year'] = $this->getOrderData($startTime, $endTime, $request);
        $filterData['prev_year'] = $this->getOrderData($prevStartTime, $prevEndTime, $request);

        return view('admin::dashboard', [
            'filterData' => $filterData,
        ]);
    }

    private function getOrderData($startTime, $endTime, $request)
    {
        $orders = $this->orders->reset()->query()->with('items')
            ->whereHas('items', function ($q) {
                $q->where('amount', '>', 0)->whereNull('gift_card_id');
            })
            ->whereRaw('DATE(created_at) >= ?', [$startTime])
            ->whereRaw('DATE(created_at) <= ?', [$endTime])
            ->where('cancelled', false)
            ->get();

        $countOrders = count($orders);

        $totalAmount = $orders->reduce(function ($sum, Order $order) {
            return $sum + $order->items()->where('amount', '>', 0)->get()
                    ->sum(function (OrderItem $orderItem) {
                    return $orderItem->amount * $orderItem->quantity;
                });
        }, 0);

        $orders = $this->orders->reset()->query()->with('items')
            ->whereHas('items', function ($q) {
                $q->where('amount', '>', 0)->whereNull('gift_card_id');
            })
            ->whereRaw('DATE(created_at) >= ?', [$startTime])
            ->whereRaw('DATE(created_at) <= ?', [$endTime])
            ->where('rebooked', false)
            ->get();

        if ($request->include_booking_fee) {
            $totalAmount += config('fees.booking_fee_to_kkj') * count($orders);
        }

        return [
            'total_amount_formatted' => number_format($totalAmount, 0, ',', ' '),
            'total_amount' => $totalAmount,
            'order_count' => $countOrders,
        ];
    }
}
