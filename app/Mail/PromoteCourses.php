<?php namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\Course;
use Jakten\Models\Order;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class OrderCreated
 * @property KKJTelegramBotService telegramBotService
 * @package Jakten\Mail
 */
class PromoteCourses extends AbstractMail
{

    use ClassResolver;

    /**
     * @var null
     */
    public $course;

    /**
     * @var string
     */
    public $toEmail;

    /**
     * Create a new message instance.
     *
     * @param Order $order
     */
    public function __construct(Course $course, string $email = null)
    {
        $this->onQueue('queue-'  . env('APP_ENV') . '-email');
        $this->toEmail = $email;
        $this->course = $course;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Dags för nästa steg mot körkortet!';
        return $this->markdown('email::order.promote')->to($this->toEmail)->subject($subject);
    }
}
