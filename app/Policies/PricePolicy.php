<?php namespace Jakten\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Jakten\Models\User;

/**
 * Class PricePolicy
 * @package Jakten\Policies
 */
class PricePolicy
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
        } elseif ($user->isStudent()) {
            return false;
        }

        return null;
    }

    /**
     * Determine whether the user can view the price.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\Price  $price
     *
     * @return mixed
     */
    public function view(User $user, Price $price)
    {
        return $user->organization->schools->contains('id', $price->school_id);
    }

    /**
     * Determine whether the user can create prices.
     *
     * @param  \Jakten\Models\User  $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the price.
     *
     * @param  \Jakten\Models\User  $user
     * @param  $price
     *
     * @return mixed
     */
    public function update(User $user, $price)
    {
        return $user->organization->schools->contains('id', $price->school_id);
    }

    /**
     * Determine whether the user can delete the price.
     *
     * @param  \Jakten\Models\User  $user
     * @param  $price
     *
     * @return mixed
     */
    public function delete(User $user, $price)
    {
        return $user->organization->schools->contains('id', $price->school_id);
    }
}
