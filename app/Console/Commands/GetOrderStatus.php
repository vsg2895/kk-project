<?php

namespace Jakten\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Jakten\Models\Order;
use Jakten\Services\Payment\Klarna\KlarnaService;

class GetOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:status {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Order Status from Klarna by order id';

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
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

            $expiresAt = new Carbon($klarnaOrder['expires_at']);

            if (!$this->order->klarna_aexpires_at) {
                $this->order->update(['klarna_expires_at' => $expiresAt]);
            }
        } catch (\Klarna_Checkout_ApiErrorException $exception) {
            $this->output->error($exception->getMessage());
        }
    }
}
