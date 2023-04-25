<?php namespace Jakten\Presenters;

use Illuminate\Support\Collection;
use Jakten\Models\Course;

/**
 * Class SearchedCourses
 * @package Jakten\Presenters
 */
class SearchedCourses
{
    /**
     * @param Collection|Course[] $courses
     *
     * @return Collection|Course[]
     */
    public function format(Collection $courses)
    {
        $courses = $courses->groupBy(function (Course $course) {
            return $course->start_time->toDateString();
        });

        return $courses;
    }
}
