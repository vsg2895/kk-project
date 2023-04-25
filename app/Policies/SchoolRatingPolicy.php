<?php namespace Jakten\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Jakten\Models\{SchoolRating, User};

/**
 * Class SchoolRatingPolicy
 * @package Jakten\Policies
 */
class SchoolRatingPolicy
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
     * Determine whether the user can view the schoolRating.
     *
     * @param  User  $user
     * @param  SchoolRating  $schoolRating
     *
     * @return mixed
     */
    public function view(User $user, SchoolRating $schoolRating)
    {
        return $user->id === $schoolRating->user_id;
    }

    /**
     * Determine whether the user can create schoolRatings.
     *
     * @param  User  $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isStudent();
    }

    /**
     * Determine whether the user can update the schoolRating.
     *
     * @param  User  $user
     * @param  SchoolRating  $schoolRating
     *
     * @return mixed
     */
    public function update(User $user, SchoolRating $schoolRating)
    {
        return $user->id === $schoolRating->user_id;
    }

    /**
     * Determine whether the user can delete the schoolRating.
     *
     * @param  User  $user
     * @param  SchoolRating  $schoolRating
     *
     * @return mixed
     */
    public function delete(User $user, SchoolRating $schoolRating)
    {
        return $user->id === $schoolRating->user_id;
    }
}
