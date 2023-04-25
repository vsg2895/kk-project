<?php namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\{Course, User};
use Jakten\Services\KKJTelegramBotService;

/**
 * Class OrderFailed
 * @property KKJTelegramBotService telegramBotService
 * @package Jakten\Mail
 */
class OrderFailed extends AbstractMail
{

    use ClassResolver;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Course
     */
    public $course;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param Course|null $course
     */
    public function __construct(User $user, Course $course = null)
    {
        $this->setQueue();
        $this->user = $user;
        $this->course = $course;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email::order.failed')
            ->to($this->user->email)->subject('Bokning kunde ej genomföras Körkortsjakten');
    }
}
