<?php namespace Jakten\Listeners;

use Jakten\Events\OrderFailed;
use Illuminate\Support\Facades\Log;
use Jakten\Services\Payment\Klarna\KlarnaService;

/**
 * Class CancelKlarnaOrder
 * @package Jakten\Listeners
 */
class CancelKlarnaOrder
{
    /**
     * @var KlarnaService
     */
    protected $klarnaService;

    /**
     * cancelKlarnaOrder constructor.
     * @param KlarnaService $klarnaService
     */
    public function __construct(KlarnaService $klarnaService)
    {
        $this->klarnaService = $klarnaService;
    }

    /**
     * Handle the event.
     *
     * @param  OrderFailed  $event
     * @return void
     */
    public function handle(OrderFailed $event)
    {
        try {
            $organization = $event->course->school->organization;
            $this->klarnaService->cancelReservation(
                config('klarna.kkj_payment_id'),
                config('klarna.kkj_payment_secret'),
                $event->klarnaPaymentId
            );
        }catch (\Exception $e)
        {
            Log::info("(event) Handle event", [
                "class" => __CLASS__,
                "event" => "OrderFailed",
                "order" => ["Course id" => $event->course->id,
                    "Klarna Payment Id" => $event->klarnaPaymentId,
                    "User email" => $event->user->email,
                    "User id" => $event->user->id,
                ],
                "exception" => $e->getMessage(),
                "exception_line" => $e->getLine(),
            ]);
        }
    }
}
