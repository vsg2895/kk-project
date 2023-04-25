<?php namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Jakten\Console\Progress;
use Jakten\Mail\DeliverCoursesReport;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\OrderService;
use Jakten\Repositories\OrderItemRepository;
use Jakten\Models\Order;

/**
 * Class DeliverCourses
 * @package Jakten\Console\Commands
 */
class DeliverCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kkj:deliver_courses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @param KKJTelegramBotService $telegramBotService
     */
    public function handle(OrderService $orderService, OrderItemRepository $orderItemRepository, KKJTelegramBotService $telegramBotService)
    {
        $collection = $orderItemRepository
            ->notDelivered()
            ->isCourseBooking()
            ->active()
            ->hasExternalId()
            ->getCoursesSinceTomorrow()
            ->get();

        $data = [];
        $logData = [];

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

        foreach ($data as $orderId => $items) {
            try {
                $order = Order::query()->findOrFail($orderId);
                $orderService->updateOrderItems($order, $items);
                $this->info('Items for orderID ' . $orderId . ' has been delivered');
                $logData[] = 'Items for orderID ' . $orderId . ' has been delivered';
            } catch (\Exception $e) {
                Log::error('Failed to automatically deliver order items', [
                    'orderID' => $orderId,
                    'items' => $items,
                    'code' => $e->getCode(),
                    'error' => $e->getMessage(),
                    'trace' => (in_array($e->getCode(), [9107, 3211])) ? $e->getTrace() : '' // @see https://developers.klarna.com/en/gb/kco-v2/error-codes#sweden
                ]);
                $this->error('Failed to deliver for ' . $orderId . '. ' . $e->getMessage());
                $logData[] = 'Failed to deliver for ' . $orderId . '. ' . $e->getMessage();
            }
        }

        Mail::to('info@korkortsjakten.se')->send(new DeliverCoursesReport($logData));
    }
}
