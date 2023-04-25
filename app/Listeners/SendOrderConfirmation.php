<?php namespace Jakten\Listeners;

use Illuminate\Support\Facades\Mail;
use Jakten\Events\NewOrder;
use Jakten\Mail\OrderCreated;
use Illuminate\Support\Facades\Log;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class SendOrderConfirmation
 * @package Jakten\Listeners
 */
class SendOrderConfirmation
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
     *
     * @return void
     */
    public function handle(NewOrder $event)
    {
        Log::info("(event) Handle event", [
            "class" => __CLASS__,
            "event" => "NewOrder",
            "mail" => "OrderCreated",
            "order" => ["id" => $event->order->id, "email" => $event->order->user->email]
        ]);

        $emails[] = $event->order->user->email;

        foreach ($event->order->items as $item) {
            if ($item->participant) {
                $emails[] = $item->participant->email;
            }

            if ($item->packageParticipant) {
                $emails[] = $item->packageParticipant->email;
            }
        }

        foreach (array_unique($emails) as $email) {
            Mail::send(new OrderCreated($event->order, $email));
        }

    }
}
