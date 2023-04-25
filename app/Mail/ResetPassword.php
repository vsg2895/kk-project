<?php namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\User;
use Jakten\Services\KKJTelegramBotService;
use Telegram\Bot\Api;

/**
 * Class ResetPassword
 * @property KKJTelegramBotService telegramBotService
 * @package Jakten\Mail
 */
class ResetPassword extends AbstractMail
{

    use ClassResolver;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string $token
     */
    public $token;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param $token
     */
    public function __construct(User $user, $token)
    {
        $this->setQueue();
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email::user.reset_password')
            ->to($this->user->email)->subject('Återställ lösenord');
    }
}
