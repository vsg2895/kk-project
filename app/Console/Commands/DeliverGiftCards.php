<?php namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Jakten\Console\Progress;
use Jakten\Services\OrderService;
use Jakten\Repositories\OrderItemRepository;
use Jakten\Models\Order;

/**
 * Class DeliverGiftCards
 * @package Jakten\Console\Commands
 */
class DeliverGiftCards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kkj:deliver_gift_cards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make giftcard purchases delivered at Klarna';

    /**
     * @var Progress|null
     */
    private $progress = null;

    /**
     * DeliverCourses constructor.
     *
     * @param Progress $progress
     */
    public function __construct(Progress $progress)
    {
        parent::__construct();
        $this->progress = $progress;
    }

    /**
     * @param OrderService $orderService
     * @param OrderItemRepository $orderItemRepository
     */
    public function handle(OrderService $orderService, OrderItemRepository $orderItemRepository)
    {
        $collection = $orderItemRepository
            ->notDelivered()
            ->isGiftCard()
            ->active()
            ->hasExternalId()
            ->get();

        $data = [];

        /**
         * @var $item \Jakten\Models\OrderItem
         */
        foreach ($collection as $item) {
            $itemTmp['id'] = $item->id;
            $itemTmp['delivered'] = 1;
            $itemTmp['cancelled'] = 0;
            $itemTmp['credited'] = 0;
            $data[$item->order->id][] = $itemTmp;
        }

        foreach ($data as $orderId => $items)
        {
            try {
                $order = Order::query()->findOrFail($orderId);
                $orderService->updateOrderItems($order, $items);
                $this->info('Items for orderID ' . $orderId . ' has been delivered');
            } catch (\Exception $e) {
                Log::error('Failed to automatically deliver order items', [
                    'orderID' => $orderId,
                    'items' => $items,
                    'error' => $e->getMessage()
                ]);
                $this->error('Failed to deliver for '. $orderId . '. ' . $e->getMessage(), '. ' . $e->getFile() . ', ' . $e->getLine());
            }
        }
    }
}
