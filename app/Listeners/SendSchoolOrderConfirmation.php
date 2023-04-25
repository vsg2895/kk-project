<?php namespace Jakten\Listeners;

use Illuminate\Support\Facades\Mail;
use Jakten\Events\NewOrder;
use Jakten\Mail\SchoolOrderCreated;
use Illuminate\Support\Facades\Log;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class SendSchoolOrderConfirmation
 *
 * @package Jakten\Listeners
 */
class SendSchoolOrderConfirmation
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
     * @param  NewOrder $event
     * @return void
     */
    public function handle(NewOrder $event)
    {
        Log::info("(event) Handle event", [
            "class" => __CLASS__,
            "event" => "NewOrder",
            "mail"  => "SchoolOrderCreated",
            "order" => ["id" => $event->order->id, "email" => $event->order->user->email],
        ]);

        $mail = new SchoolOrderCreated($event->order);
        $mail->setData();

        Mail::send($mail);
    }
}
