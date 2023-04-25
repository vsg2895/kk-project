<?php namespace Jakten\Listeners;

use Illuminate\Support\Facades\Mail;
use Jakten\Events\NewOrder;
use Jakten\Events\OrderCancelled;
use Jakten\Mail\OrderCancelled as CancelledMail;
use Illuminate\Support\Facades\Log;

/**
 * Class SendOrderCancelledConfirmation
 * @package Jakten\Listeners
 */
class SendOrderCancelledConfirmation
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
     * @param  NewOrder  $event
     *
     * @return void
     */
    public function handle(OrderCancelled $event)
    {
        Log::info("(event) Handle event", [
            "class" => __CLASS__,
            "event" => "OrderCancelled",
            "mail" => "CancelledMail",
            "order" => ["id" => $event->order->id, "email" => $event->order->user->email]
        ]);
        Mail::send(new CancelledMail($event->order));
    }
}
