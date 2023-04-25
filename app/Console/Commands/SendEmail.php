<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Jakten\Events\NewOrder;
use Jakten\Events\OrderCancelled;
use Jakten\Models\Order;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manual:send {type} {orders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /** @var Order[] */
    private $orders = [];

    /**
     * @var array
     */
    private $types = [
        'NewOrder' => NewOrder::class,
        'OrderCancelled' => OrderCancelled::class,
    ];

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
        $this->orders = Order::query()
            ->whereIn('id', explode(',', $this->argument('orders')))
            ->get();

        $this->orders->each(function (Order $order) {
            $eventType = $this->types[$this->argument('type')];

            $this->output->text("Sending an Email for Event Type `{$eventType}` and Order ID #{$order->id}");
            event(new $eventType($order));
        });
    }
}
