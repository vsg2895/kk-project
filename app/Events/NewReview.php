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
class NewReview
{
    use Dispatchable, SerializesModels;

    /**
     * @var Course
     */
    public $course;

    /**
     * @var string
     */
    public $label = 'new_review';
    /**
     * @var User
     */
    public $user;
    /**
     * @var School
     */
    public $school;
    /**
     * @var SchoolRating
     */
    public $rating;

    /**
     * NewOrder constructor.
     * @param School $school
     * @param Course $course
     * @param User $user
     * @param SchoolRating $rating
     */
    public function __construct(School $school, Course $course, User $user, SchoolRating $rating)
    {
        $this->course = $course;
        $this->school = $school;
        $this->user = $user;
        $this->rating = $rating;
    }
}
