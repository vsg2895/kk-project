<?php namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\{ConfirmationToken, User};
use Jakten\Services\KKJTelegramBotService;

/**
 * Class UserCreated
 * @property KKJTelegramBotService telegramBotService
 * @package Jakten\Mail
 */
class UserCreated extends AbstractMail
{
    use ClassResolver;

    /**
     * @var User
     */
    public $user;

    /**
     * @var ConfirmationToken
     */
    public $token;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param ConfirmationToken $token
     */
    public function __construct(User $user, ConfirmationToken $token)
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
        return $this->markdown('email::user.created')
            ->to($this->user->email)->subject('Konto skapat');
    }
}
