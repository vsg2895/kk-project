<?php namespace Jakten\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Jakten\Console\Progress;
use Jakten\Mail\DeliverCoursesReport;
use Jakten\Repositories\OrderRepository;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\OrderService;
use Jakten\Models\Order;
use Jakten\Services\Payment\Klarna\KlarnaService;

/**
 * Class DeliverCourses
 * @package Jakten\Console\Commands
 */
class ExtendExpiresAtKlarnaOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:extend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extend klarna order expires at date';

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
     * @param OrderRepository $orderIRepository
     * @param KKJTelegramBotService $telegramBotService
     * @throws \KlarnaException
     */
    public function handle(OrderRepository $orderIRepository, KKJTelegramBotService $telegramBotService)
    {

        $collection = $orderIRepository
            ->getKlarnaExpiresOrders()
            ->get();

        $telegramBotService->log('extend_order_selected', ['qty' => $collection ? count($collection) : 0]);

        /**
         * @var $item \Jakten\Models\Order
         */
        foreach ($collection as $item) {
            /** @var KlarnaService $klarna */
            $klarna = resolve(KlarnaService::class);

            try {
                $newDate = $klarna->updateOrderExpirationDate($item);
                $expiresAt = new Carbon($newDate);

                $item->update(['klarna_expires_at' => $expiresAt]);

                $this->output->success("{$expiresAt->format('Y-m-d\TH:m:s')}");
                $telegramBotService->log('extend_order_success_'.$item->id, [
                    'expires_at' => "{$expiresAt->format('Y-m-d\TH:m:s')}"
                ]);
            } catch (\Exception $exception) {
                $telegramBotService->log('extend_order_failed_'.$item->id, ['order' => $item->klarna_expires_at, 'error' => $exception->getMessage()]);
                $this->output->error($exception->getMessage());
            }
        }
    }
}
