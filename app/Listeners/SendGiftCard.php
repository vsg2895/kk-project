<?php namespace Jakten\Listeners;

use Illuminate\Support\Facades\Mail;
use Jakten\Events\NewGiftCard;
use Jakten\Mail\GiftCardCreated;
use Jakten\Repositories\UserRepository;

/**
 * Class SendNewOrderNotify
 * @package Jakten\Listeners
 */
class SendGiftCard
{
    /**
     * SendNewOrderNotify constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the event.
     *
     * @param NewGiftCard $event
     */
    public function handle(NewGiftCard $event)
    {
        Mail::send(with(new GiftCardCreated($event->giftCard)));
    }
}
