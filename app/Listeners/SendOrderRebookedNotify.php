<?php

namespace Jakten\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Jakten\Events\NewOrder;
use Jakten\Events\OrderRebooked;
use Jakten\Mail\NotifyOrderRebooked as NotifyOrderRebooked;
use Jakten\Repositories\UserRepository;

class SendOrderRebookedNotify
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    /**
     * @var UserRepository
     */
    private $userRepository;


    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
            "mail" => "NotifyOrderRebooked",
            "order" => ["id" => $event->order->id, "email" => "selectAdminsChannels(new_order)"]
        ]);
        $admins = $this->userRepository->selectAdminsChannels('new_order')->get();

        foreach ($admins as $admin) {
            Mail::send(new NotifyOrderRebooked($event->order, $admin));
        }

    }
}
