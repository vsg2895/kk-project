<?php namespace Jakten\Listeners;

use Jakten\Events\OrderFailed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Jakten\Mail\OrderFailed as MailOrderFailed;
use Jakten\Services\KKJTelegramBotService;

class SendOrderFailedMessage
{
    /**
     * SendOrderFailedMessage constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param OrderFailed $event
     */
    public function handle(OrderFailed $event)
    {
        Log::info("(event) Handle event", [
            "class" => __CLASS__,
            "event" => "OrderFailed",
            "mail" => "OrderFailed",
            "order" => ["Course id" => $event->course->id,
                "Klarna Payment Id" => $event->klarnaPaymentId,
                "User email" => $event->user->email,
                "User id" => $event->user->id
            ]
        ]);
        Mail::send(new MailOrderFailed($event->user, $event->course));
    }
}
