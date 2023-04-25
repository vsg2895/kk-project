<?php

namespace Jakten\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Jakten\Events\NewOrder;
use Jakten\Events\OrderRebooked;
use Jakten\Mail\OrderRebooked as OrderRebookedMail;

class SendOrderRebookedConfirmation
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
    public function handle(OrderRebooked $event)
    {
        Log::info("(event) Handle event", [
            "class" => __CLASS__,
            "event" => "OrderRebooked",
            "mail" => "OrderRebookedMail",
            "order" => ["id" => $event->order->id, "email" => $event->order->user->email]
        ]);
        Mail::send(new OrderRebookedMail($event->order));
    }
}
