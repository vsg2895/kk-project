<?php namespace Jakten\Listeners;

use Jakten\Events\NewOrder;
use Jakten\Repositories\UserRepository;
use Jakten\Notifications\NewOrderNotify;
use Illuminate\Support\Facades\Notification;

/**
 * Class SendNewOrderNotify
 * @package Jakten\Listeners
 */
class SendNewOrderNotify
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

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
     * @param  NewOrder  $event
     * @return void
     */
    public function handle(NewOrder $event)
    {
        if ($event->order->school) {
            $users = $this->userRepository->selectAdminsChannels($event->label)->get();
            //$users = $this->userRepository->onlyAdmins()->get();

//            $schoolUser = $this->userRepository->getUserByOrgId($event->order->school->organization_id)->first();
//
//            if ($schoolUser) {
//                $users->push($schoolUser);
//            }

            if ($users) {
                Notification::send($users, new NewOrderNotify($event));
            }
        }

    }
}
