<?php namespace Jakten\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Jakten\Models\{School, User};

/**
 * Class SchoolPolicy
 * @package Jakten\Policies
 */
class SchoolPolicy
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
     * Determine whether the user can view the school.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\School  $school
     *
     * @return mixed
     */
    public function view(User $user, School $school)
    {
        return $user->organization->schools->contains('id', $school->id);
    }

    /**
     * Determine whether the user can create schools.
     *
     * @param  \Jakten\Models\User  $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the school.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\School  $school
     *
     * @return mixed
     */
    public function update(User $user, School $school)
    {
        return $user->organization->schools->contains('id', $school->id);
    }

    /**
     * Determine whether the user can delete the school.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\School  $school
     *
     * @return mixed
     */
    public function delete(User $user, School $school)
    {
        return false;
    }
}
