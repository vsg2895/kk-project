<?php namespace Jakten\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Jakten\Models\Course;
use Jakten\Models\Order;
use Jakten\Models\School;
use Jakten\Models\SchoolRating;
use Jakten\Models\User;

/**
 * Class NewOrder
 * @package Jakten\Events
 */
class ReviewVerified
{
    use Dispatchable, SerializesModels;

    /**
     * @var Course
     */
    public $course;

    /**
     * @var string
     */
    public $label = 'review_verified';

    /**
     * @var SchoolRating
     */
    public $rating;

    /**
     * NewOrder constructor.
     * @param SchoolRating $rating
     */
    public function __construct(SchoolRating $rating)
    {
        $this->rating = $rating;
    }
}
