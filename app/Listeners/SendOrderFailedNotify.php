<?php namespace Jakten\Listeners;

use Jakten\Events\OrderFailed;
use Jakten\Repositories\UserRepository;
use Jakten\Notifications\OrderFailedNotify;
use Illuminate\Support\Facades\Notification;

/**
 * Class SendOrderFailedNotify
 * @package Jakten\Listeners
 */
class SendOrderFailedNotify
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * SendOrderFailedNotify constructor.
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
     * @param  OrderFailed  $event
     * @return void
     */
    public function handle(OrderFailed $event)
    {
        $users = $this->userRepository->selectAdminsChannels($event->label)->get();
        //$users = $this->userRepository->onlyAdmins()->get();
        Notification::send($users, new OrderFailedNotify($event));
    }
}
