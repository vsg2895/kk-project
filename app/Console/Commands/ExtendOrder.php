<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Jakten\Models\Order;
use Jakten\Services\Payment\Klarna\KlarnaService;

class ExtendOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:extend {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expiration date by order id';

    /** @var Order */
    private $order;

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
        /** @var Order order */
        $this->order = Order::whereId($this->argument('id'))->first();

        /** @var KlarnaService $klarna */
        $klarna = resolve(KlarnaService::class);

        try {
            $klarnaOrder = $klarna->getOrder($this->order->school, $this->order->external_order_id);
            $klarnaOrder->fetch();

            $this->output->success("{$klarnaOrder['status']}");
            $this->output->success("{$klarnaOrder['expires_at']}");

            $newDate = $klarna->updateOrderExpirationDate($this->order);
            $this->output->success("{$newDate}");
        } catch (\Klarna_Checkout_ApiErrorException $exception) {
            $this->output->error($exception->getMessage());
        }
    }
}
