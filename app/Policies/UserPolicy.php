<?php namespace Jakten\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Jakten\Models\User;

/**
 * Class UserPolicy
 * @package Jakten\Policies
 */
class UserPolicy
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
     * @param User $loggedIn
     * @param User $user
     * @return bool
     */
    public function view(User $loggedIn, User $user)
    {
        return $loggedIn->organization_id === $user->organization_id;
    }
}