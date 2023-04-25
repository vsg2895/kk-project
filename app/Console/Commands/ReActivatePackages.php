<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Jakten\Jobs\ActivatePackageOrders;
use Jakten\Models\Order;
use Jakten\Models\OrderItem;

class ReActivatePackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packages:reactivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reactivation of packages';

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
        Order::query()
            ->where('handled', false)
            ->get()
            ->filter(function (Order $order) {
                return $order->isPackageOrder();
            })
            ->each(function (Order $order) {
                ActivatePackageOrders::dispatch($order);
            });

    }
}
