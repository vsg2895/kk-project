<?php namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\Order;
use Jakten\Models\School;
use Jakten\Models\SchoolRating;
use Jakten\Models\User;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class OrderCreated
 * @property KKJTelegramBotService telegramBotService
 * @package Jakten\Mail
 */
class NewReview extends AbstractMail
{

    use ClassResolver;

    /**
     * @var Order
     */
    public $order;

    /**
     * @var array
     */
    public $courses;

    /**
     * @var string
     */
    public $label = 'new_review';

    /**
     * @var User
     */
    public $user;

    /**
     * @var \Jakten\Events\NewReview
     */
    public $event;

    /**
     * Create a new message instance.
     *
     * @param \Jakten\Events\NewReview $event
     * @param User $user
     */
    public function __construct(\Jakten\Events\NewReview $event, User $user)
    {
        $this->onQueue('queue-' . env('APP_ENV') . '-email');
        $this->event = $event;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Nya recensioner';
        return $this->markdown('email::review.admin')->to($this->user->email)->subject($subject);
    }
}
