<?php namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\User;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class NotifyRegister
 * @property KKJTelegramBotService telegramBotService
 * @package Jakten\Mail
 */
class NotifyRegister extends AbstractMail
{

    use ClassResolver;

    /**
     * @var $notifyUser
     */
    protected $notifyUser;

    /**
     * @var $newUser
     */
    protected $newUser;

    /**
     * NotifyRegister constructor.
     *
     * @param User $notifyUser
     * @param User $newUser
     */
    public function __construct(User $notifyUser, User $newUser)
    {
        $this->setQueue();
        $this->notifyUser = $notifyUser;
        $this->newUser = $newUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email::notify.register', ['user' => $this->newUser])
            ->to($this->notifyUser->email)->subject(__('notify.notification') . __('notify.new_register'));
    }
}
