<?php namespace Jakten\Listeners;

use Illuminate\Support\Facades\Mail;
use Jakten\Events\OrderCancelled;
use Jakten\Mail\SchoolOrderCancelled;
use Illuminate\Support\Facades\Log;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class SendSchoolOrderCancelledConfirmation
 * @package Jakten\Listeners
 */
class SendSchoolOrderCancelledConfirmation
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
     * @param OrderCancelled $event
     */
    public function handle(OrderCancelled $event)
    {
        Log::info("(event) Handle event", [
            "class" => __CLASS__,
            "event" => "OrderCancelled",
            "mail" => "SchoolOrderCancelled",
            "order" => ["id" => $event->order->id, "email" => $event->order->user->email]
        ]);
        Mail::send(new SchoolOrderCancelled($event->order));
    }
}
