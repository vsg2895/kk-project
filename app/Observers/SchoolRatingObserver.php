<?php namespace Jakten\Observers;

use Jakten\Models\{School, SchoolRating};

/**
 * Class SchoolRatingObserver
 * @package Jakten\Observers
 */
class SchoolRatingObserver
{
    /**
     * Listen to the SchoolRating saved event.
     *
     * @param  SchoolRating  $schoolRating
     *
     * @return void
     */
    public function saved(SchoolRating $schoolRating)
    {
        $this->setAverageRating($schoolRating->school);
    }

    /**
     * Listen to the User deleting event.
     *
     * @param  SchoolRating $schoolRating
     *
     * @return void
     */
    public function deleted(SchoolRating $schoolRating)
    {
        $this->setAverageRating($schoolRating->school);
    }

    /**
     * @param School $school
     */
    protected function setAverageRating(School $school)
    {
        $ratings = $school->ratings;
        $average = $ratings->average(function ($rating) {
            return $rating->rating;
        });

        if ($ratings->count() > 4) {
            $school->average_rating = $average;
        } else {
            $school->average_rating = null;
        }

        $school->save();
    }
}
