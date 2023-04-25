<?php namespace Jakten\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Jakten\Models\{Course, User};

/**
 * Class CoursePolicy
 * @package Jakten\Policies
 */
class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param $ability
     * @return bool|null
     */
    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the course.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\Course  $course
     *
     * @return mixed
     */
    public function view(User $user, Course $course)
    {
        return $this->madeAtOrganization($user, $course);
    }

    /**
     * @param User $user
     * @param Course $course
     *
     * @return bool
     */
    private function madeAtOrganization(User $user, Course $course)
    {
        if ($user->isOrganizationUser()) {
            return $user->organization->schools->contains('id', $course->school_id);
        }

        return false;
    }

    /**
     * Determine whether the user can create courses.
     *
     * @param  \Jakten\Models\User  $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isOrganizationUser() && $user->organization->schools->count();
    }

    /**
     * Determine whether the user can update the course.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\Course  $course
     *
     * @return mixed
     */
    public function update(User $user, Course $course)
    {
        return $this->madeAtOrganization($user, $course);
    }

    /**
     * Determine whether the user can delete the course.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\Course  $course
     *
     * @return mixed
     */
    public function delete(User $user, Course $course)
    {
        return $this->madeAtOrganization($user, $course);
    }
}
