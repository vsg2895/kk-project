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
class ReviewVerified extends AbstractMail
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
    public $label = 'review_verified';

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
     * @param \Jakten\Events\ReviewVerified $event
     */
    public function __construct(\Jakten\Events\ReviewVerified $event)
    {
        $this->onQueue('queue-' . env('APP_ENV') . '-email');
        $this->event = $event;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Ni har fått ett omdöme på er sida';
        return $this->markdown('email::review.school')->to($this->event->rating->school->contact_email)->subject($subject);
    }
}
