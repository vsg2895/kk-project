<?php namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\Order;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class OrderCreated
 * @property KKJTelegramBotService telegramBotService
 * @package Jakten\Mail
 */
class OrderCreated extends AbstractMail
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
     * @var bool
     */
    public $isReminder;

    /**
     * @var string
     */
    public $toEmail;

    /**
     * Create a new message instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order, string $email = null, bool $isReminder = false)
    {
        $courseOrderItems = $order->items->where('course_id', '!=', null)->all();
        $courses = [];
        foreach ($courseOrderItems as $courseOrderItem) {
            if ($courseOrderItem->course && ($email === $courseOrderItem->participant->email || $email === $order->user->email || $email === null)) {
                $presented = false;
                foreach ($courses as $course) {
                    if ($courseOrderItem->course->id === $course->id) {
                        $presented = true;
                    }
                }

                if ($presented) {
                    continue;
                }

                $courses[] = $courseOrderItem->course;
            }
        }

        $this->onQueue('queue-' . env('APP_ENV') . '-email');
        $this->order = $order;
        $this->toEmail = $email;
        $this->isReminder = $isReminder;
        $this->courses = $courses;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->order->isGiftCardOrder() ? 'Presentkort Körkortsjakten' : 'Bokningsbekräftelse Körkortsjakten';
        $subject = $this->isReminder ? 'Snart dags för din kurs' : $subject;
        return $this->markdown('email::order.created')->to($this->toEmail ?: $this->order->user->email)->subject($subject);
    }
}
