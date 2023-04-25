<?php namespace Jakten\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Jakten\Models\{Course, User};

/**
 * Class OrderFailed
 * @package Jakten\Events
 */
class OrderFailed
{
    use Dispatchable, SerializesModels;

    /**
     * @var Course
     */
    public $course;

    /**
     * @var User
     */
    public $user;

    /**
     * @var String
     */
    public $klarnaPaymentId;

    /**
     * @var string
     */
    public $label = 'order_failed';

    /**
     * OrderFailed constructor.
     * @param Course $course
     * @param $klarnaPaymentId
     * @param User $user
     */
    public function __construct(Course $course = null, $klarnaPaymentId, User $user)
    {
        $this->user = $user;
        $this->course = $course;
        $this->klarnaPaymentId = $klarnaPaymentId;
    }
}
