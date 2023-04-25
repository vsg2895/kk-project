<?php namespace Jakten\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Jakten\Models\{Order, User};

/**
 * Class OrderPolicy
 * @package Jakten\Policies
 */
class OrderPolicy
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
     * Determine whether the user can view the order.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\Order  $order
     *
     * @return mixed
     */
    public function view(User $user, Order $order)
    {
        return $user->id === $order->user_id || $this->madeAtOrganization($user, $order);
    }

    /**
     * @param User $user
     * @param Order $order
     * @return bool
     */
    private function madeAtOrganization(User $user, Order $order)
    {
        if ($user->isOrganizationUser()) {
            return $user->organization->schools->contains('id', $order->school_id);
        }

        return false;
    }

    /**
     * Determine whether the user can create orders.
     *
     * @param  \Jakten\Models\User  $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isStudent();
    }

    /**
     * Determine whether the user can update the order.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\Order  $order
     *
     * @return mixed
     */
    public function update(User $user, Order $order)
    {
        return $user->id === $order->user_id || $this->madeAtOrganization($user, $order);
    }

    /**
     * Determine whether the user can delete the order.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\Order  $order
     *
     * @return mixed
     */
    public function delete(User $user, Order $order)
    {
        return $user->id === $order->user_id;
    }
}
