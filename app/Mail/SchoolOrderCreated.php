<?php namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\Course;
use Jakten\Models\Log;
use Jakten\Models\Order;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class SchoolOrderCreated
 *
 * @property KKJTelegramBotService telegramBotService
 * @package Jakten\Mail
 */
class SchoolOrderCreated extends AbstractMail
{
    use ClassResolver;

    /**
     * @var Order
     */
    public $order;

    /** @var bool|Course */
    public $course = false;

    /**
     * Create a new message instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->setQueue();
        $this->order = $order;

        if ($course = $order->items->where('course_id', '!=', null)->first()) {
            $this->course = $course->course;
        } else {
            $this->course = null;
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email::order.school_created', ['order' => $this->order, 'course' => $this->course]);
    }

    /**
     * @return SchoolOrderCreated
     */
    public function setData()
    {
        $school = $this->order->school;
        $to = $school->booking_email ?? config('mail.contact_email');
        $subject = $this->order->isGiftCardOrder() ? 'Presentkort Körkortsjakten' : 'Kursbokning Körkortsjakten';

        return $this->to($to)->subject($subject);
    }
}
