<?php namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\Course;
use Jakten\Models\Order;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class SchoolOrderCancelled
 * @property KKJTelegramBotService telegramBotService
 * @package Jakten\Mail
 */
class SchoolOrderRebooked extends AbstractMail
{

    use ClassResolver;

    public $order;

    /** @var null|Course */
    public $course;

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
        return $this->to($this->course ? $this->course->school->booking_email : $this->order->school->booking_email)
            ->subject('Kursavbokning Körkortsjakten')
            ->markdown('email::order.school_cancelled', ['order' => $this->order]);
    }
}
