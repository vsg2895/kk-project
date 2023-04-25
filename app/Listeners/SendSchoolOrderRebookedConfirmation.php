<?php namespace Jakten\Listeners;

use Illuminate\Support\Facades\Mail;
use Jakten\Events\OrderRebooked;
use Jakten\Mail\SchoolOrderRebooked;
use Illuminate\Support\Facades\Log;

/**
 * Class SendSchoolOrderCancelledConfirmation
 * @package Jakten\Listeners
 */
class SendSchoolOrderRebookedConfirmation
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param OrderRebooked $event
     */
    public function handle(OrderRebooked $event)
    {
        Log::info("(event) Handle event", [
            "class" => __CLASS__,
            "event" => "OrderRebooked",
            "mail" => "SchoolOrderRebooked",
            "order" => ["id" => $event->order->id, "email" => $event->order->school->booking_email]
        ]);
        Mail::send(new SchoolOrderRebooked($event->order));
    }
}
