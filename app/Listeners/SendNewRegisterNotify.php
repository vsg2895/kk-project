<?php namespace Jakten\Listeners;

use Jakten\Events\NewRegistration;
use Jakten\Repositories\UserRepository;
use Jakten\Notifications\NewRegisterNotify;
use Illuminate\Support\Facades\Notification;

/**
 * Class SendNewRegisterNotify
 * @package Jakten\Listeners
 */
class SendNewRegisterNotify
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * SendNewRegisterNotify constructor.
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
     * @param  NewRegistration  $event
     * @return void
     */
    public function handle(NewRegistration $event)
    {
        $users = $this->userRepository->selectAdminsChannels($event->label)->get();
        //$users = $this->userRepository->onlyAdmins()->get();
        Notification::send($users, new NewRegisterNotify($event));
    }
}
