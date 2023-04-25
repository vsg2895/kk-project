<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Jakten\Models\Order;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\Payment\Klarna\KlarnaService;
use Klarna;
use KlarnaFlags;
use KlarnaException;

class KlarnaManualActivation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'klarna:activate {--id=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate Klarna Order';

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
     * @param $paymentId
     * @param $paymentSecret
     * @return \Klarna
     * @throws \KlarnaException
     */
    private function initKlarna($paymentId, $paymentSecret)
    {
        $klarna = new Klarna();
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
     * @return \Klarna
     * @throws \KlarnaException
     */
    private function initKlarnaFromOrder(Order $order)
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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orderIds = $this->option('id');
        $orderIds = count($orderIds) <= 1 ? (array)$orderIds : $orderIds;

        /** @var Collection $orders */
        $orders = Order::query()->whereIn('id', $orderIds)->get();
        $orders->each(function (Order $order) {
            try {
                if ($order->isKlarnaV3()) {
                    /** @var Klarna $klarnaService */
                    $klarnaService = $this->initKlarnaFromOrder($order);
                    $klarnaService->activate($order->external_reservation_id,
                        null,
                        KlarnaFlags::RSRV_PRESERVE_RESERVATION);
                } else {
                    /** @var KlarnaService $klarna */
                    $klarna = resolve(KlarnaService::class);
                    $klarna->captureOrderItems($order, $order->items()->pluck('id')->toArray());
                }
            } catch (KlarnaException $exception) {
                $this->output->text("Error! : {$exception->getMessage()}");
            }
        });
    }
}
