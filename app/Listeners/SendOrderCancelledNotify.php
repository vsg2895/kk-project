<?php namespace Jakten\Listeners;

use Jakten\Events\OrderCancelled;
use Jakten\Repositories\UserRepository;
use Illuminate\Support\Facades\Notification;
use Jakten\Notifications\OrderCancelledNotify;

/**
 * Class SendOrderCancelledNotify
 * @package Jakten\Listeners
 */
class SendOrderCancelledNotify
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * SendOrderCancelledNotify constructor.
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
     * @param  OrderCancelled  $event
     * @return void
     */
    public function handle(OrderCancelled $event)
    {
        $users = $this->userRepository->selectAdminsChannels($event->label)->get();
        //$users = $this->userRepository->onlyAdmins()->get();

//        $schoolUser = $this->userRepository->getUserByOrgId($event->order->school->organization_id)->first();
//
//        if ($schoolUser) {
//            $users->push($schoolUser);
//        }

        if ($users) {
            Notification::send($users, new OrderCancelledNotify($event));
        }
    }
}
