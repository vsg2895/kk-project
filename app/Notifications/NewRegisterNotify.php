<?php namespace Jakten\Notifications;

use Jakten\Mail\NotifyRegister;
use Jakten\Events\NewRegistration;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class NewRegisterNotify
 * @package Jakten\Notifications
 */
class NewRegisterNotify extends Notification
{
    use NotifyTrait;

    /**
     * @var
     */
    protected $newUser;

    /**
     * NewRegisterNotify constructor.
     *
     * @param $event
     */
    public function __construct(NewRegistration $event)
    {
        $this->newUser = $event->user;
    }

    /**
     * Create message class for ['database'] channel.
     *
     * @param  mixed  $user
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     */
    public function toDatabase($user)
    {
        return new DatabaseMessage([
            'message' => __('notify.dash_new_user', [
                'name' => $this->newUser->getNameAttribute(),
                'date' => $this->newUser->created_at
            ])
        ]);
    }

    /**
     * Create message class for ['mail'] channel.
     *
     * @param $user
     * @return NotifyRegister
     */
    public function toCustomMail($user)
    {
        return new NotifyRegister($user, $this->newUser);
    }

    /**
     * Create message class for [CustomSMSChannel::class] channel.
     *
     * @return string
     */
    public function toCustomSMS()
    {
        return __('notify.notification') . __('notify.new_register');
    }
}
