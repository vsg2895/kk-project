<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Jakten\Models\Order;

class CheckOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $orders = Order::query();
        $this->output->text("Total Orders Count : {$orders->count()}");
        $orders
            ->orderByDesc('id')
            ->get()
            ->each(function (Order $order) {
                $this->output->note("Checking Order No.{$order->id}..");
                $order->update($this->getHandledAndCancelledState($order)) ?
                    $this->output->success("Order No.{$order->id} has been updated") :
                    $this->output->warning("Failed to update an Order No.{$order->id}");
            });
    }

    private function getHandledAndCancelledState(Order $order)
    {
        return [
            'handled' => $order->items()->count() === $order->items()->where('delivered', true)->count(),
            'cancelled' => $order->items()->count() === $order->items()->where('cancelled', true)->count()
        ];
    }
}
