<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Jakten\Models\Order;
use Jakten\Models\OrderItem;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\OrderService;
use Jakten\Services\Payment\Klarna\KlarnaService;

class CancelOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cancel:order {orderId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /** @var KlarnaService */
    private $klarnaService;

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * Create a new command instance.
     *
     * @param KlarnaService $klarnaService
     * @param OrderService $orderService
     */
    public function __construct(KlarnaService $klarnaService, OrderService $orderService)
    {
        parent::__construct();
        $this->klarnaService = $klarnaService;
        $this->orderService = $orderService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \KlarnaException
     */
    public function handle()
    {
        /** @var Order $order */
        $order = Order::query()->where('id', $this->argument('orderId'))->first();

        $isRefunded = $this->klarnaService->cancelOrder($order);
        $this->orderService->cancelOrder($order);

        $order->items()
            ->each(function (OrderItem $orderItem) use($order, $isRefunded) {

                switch ($orderItem->external_invoice_id && !$orderItem->credited) {
                    case true:
                        try {
                            if ($isRefunded) {
                                $this->output->success("Order Item #{$orderItem->id} with an Invoice No.{$orderItem->external_invoice_id} has been successfully credited!");
                                $orderItem->update([
                                    'credited' => true,
                                    'cancelled' => true
                                ]);

                                return;
                            }

                            $this->output->error("Failed to credit an Order Item #{$orderItem->id} with an Invoice No.{$orderItem->external_invoice_id}");
                            return;
                        } catch (\Exception $exception) {
                            $this->output->error($exception->getMessage());
                            Log::info($exception);
                        }
                        break;
                    default:
                        $orderItem->update([
                            'credited' => true,
                            'cancelled' => true
                        ]);
                        break;
                }
            });

        if ($order->all_items_cancelled) {
            $order->update(['cancelled' => true]);
        }
    }

    /**
     * @param $paymentId
     * @param $paymentSecret
     * @return \Klarna
     * @throws \KlarnaException
     */
    private function initKlarna($paymentId, $paymentSecret)
    {
        $klarna = new \Klarna();
        $klarna->config(
            $paymentId,
            $paymentSecret,
            \KlarnaCountry::SE,
            \KlarnaLanguage::SV,
            \KlarnaCurrency::SEK,
            config('app.env') === 'production' ? \Klarna::LIVE : \Klarna::BETA
        );

        return $klarna;
    }

    /**
     * @param Order $order
     * @param bool $orderManager
     * @return mixed
     * @throws \KlarnaException
     */
    private function initKlarnaFromOrder(Order $order, $orderManager = false)
    {
        if (!$order->booking_fee) {
            /** @var KKJTelegramBotService $kkjBot */
            $kkjBot = resolve(KKJTelegramBotService::class);
            $kkjBot->log('activate_klarna_from_old_order', compact('order'));

            $org = $order->school->organization;
            return $this->initKlarna((int)$org->payment_id, $org->payment_secret);
        }

        return $this->initKlarna(config('klarna.kkj_payment_id'), config('klarna.kkj_payment_secret'));
    }

    /**
     * @param Order $order
     * @return array
     */
    private function getPaymentCredentials(Order $order)
    {
        if (!$order->booking_fee) {
            /** @var KKJTelegramBotService $kkjBot */
            $kkjBot = resolve(KKJTelegramBotService::class);
            $kkjBot->log('activate_klarna_from_old_order', compact('order'));

            $org = $order->school->organization;
            [(int)$org->payment_id, $org->payment_secret];
        }

        return [config('klarna.kkj_payment_id'), config('klarna.kkj_payment_secret')];
    }

}
