<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Jakten\Models\Order;
use Jakten\Services\Payment\Klarna\KlarnaService;

class ActivateOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:activate {list}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Orders activation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ordersIds = explode(',', $this->argument('list'));
        $orders = Order::query()->whereIn('id', $ordersIds)->get();

        /** @var KlarnaService $klarna */
        $klarna = resolve(KlarnaService::class);

        $orders->each(function (Order $order) use ($klarna) {
            $this->output->text("Activating Order No.{$order->id}");

            try {
                if (!$order->isKlarnaV3()) {
                    $klarna->activateItems($order, $order->items()->pluck('id')->toArray());
                } else {
                    $klarna->captureOrderItems($order, $order->items()->pluck('id')->toArray());
                }

                $this->output->success("Order No.{$order->id} activation has been succeeded");

            } catch (\Exception $exception) {
                $this->output->error($exception->getMessage());
            }
        });
    }
}
