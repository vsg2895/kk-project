<?php

namespace Jakten\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Jakten\Models\Order;
use Jakten\Models\OrderItem;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\Payment\Klarna\KlarnaService;

/**
 * Class ActivatePackageOrders
 * @package Jakten\Jobs
 */
class ActivatePackageOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Order */
    private $order;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     * @param bool $now
     */
    public function __construct(Order $order, $now = false)
    {
        if (!$now) {
            $this->onQueue('activate-package-orders-' . env('APP_ENV'));
            $this->delay(Carbon::now()->addDays(14));
        }

        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Trying to handle Package the order #{$this->order->id}{$this->order->isPackageOrder()} {$this->order->handled}");

        if ($this->order->isPackageOrder() && !$this->order->handled) {

            /** @var KKJTelegramBotService $bot */
            $bot = resolve(KKJTelegramBotService::class);

            try {
                Log::info("Trying to handle the order #{$this->order->id}");

                /** @var KlarnaService $klarna */
                $klarna = resolve(KlarnaService::class);

                $items = collect();
                $this->order->items->each(function (OrderItem $orderItem) use (&$items) {
                    if (!$orderItem->delivered && !$orderItem->cancelled && !$orderItem->credited && !$orderItem->course_id) {
                        $items->push($orderItem);
                    }
                });

                if (!count($items)) {
                    $bot->log('packages_activation', [
                        'order_id' => $this->order->id,
                        'error' => 'No packages in the order which can be activated.',
                    ]);
                } else {
                    if (!$this->order->isKlarnaV3()) {
                        $klarna->activateItems($this->order, $items);
                    } else {
                        $klarna->captureOrderItems($this->order, $items);
                    }

                    $this->order->handleOrderIfAllItemsAreFulfilled();
                }

            } catch (\Exception $exception) {
                $bot->log('packages_activation_failed', [
                    'order_id' => $this->order->id,
                    'error' => $exception->getMessage(),
                ]);

                Log::error($exception->getMessage());
            }
        }
    }
}
