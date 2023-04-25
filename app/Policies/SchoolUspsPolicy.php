<?php namespace Jakten\Policies;

use Jakten\Models\{User, SchoolUsps};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class SchoolUspsPolicy
 * @package Jakten\Policies
 */
class SchoolUspsPolicy
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
     * Determine whether the user can view the SchoolUsps.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\SchoolUsps  $schoolUsps
     * @return mixed
     */
    public function view(User $user, SchoolUsps $schoolUsps)
    {
        return $user->organization->schools->contains('id', $schoolUsps->school_id);
    }

    /**
     * Determine whether the user can create SchoolUsps.
     *
     * @param  \Jakten\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isOrganizationUser();
    }

    /**
     * Determine whether the user can update the SchoolUsps.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\SchoolUsps  $schoolUsps
     * @return mixed
     */
    public function update(User $user, SchoolUsps $schoolUsps)
    {
        return $user->organization->schools->contains('id', $schoolUsps->school_id);
    }

    /**
     * Determine whether the user can delete the SchoolUsps.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\SchoolUsps  $schoolUsps
     * @return mixed
     */
    public function delete(User $user, SchoolUsps $schoolUsps)
    {
        return $user->organization->schools->contains('id', $schoolUsps->school_id);
    }
}
