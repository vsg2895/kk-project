<?php namespace Jakten\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Jakten\Models\{Course, OrderItem, User};

/**
 * Class OrderItemPolicy
 * @package Jakten\Policies
 */
class OrderItemPolicy
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
     * Determine whether the user can view the orderItem.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\OrderItem  $orderItem
     *
     * @return mixed
     */
    public function view(User $user, OrderItem $orderItem)
    {
        return $user->id === $orderItem->order->user_id || $this->bookedAtSchool($user, $orderItem->course);
    }

    /**
     * @param User $user
     * @param Course $course
     * @return bool
     */
    private function bookedAtSchool(User $user, Course $course)
    {
        if ($user->isOrganizationUser()) {
            return $user->organization->schools->contains('id', $course->school_id);
        }

        return false;
    }

    /**
     * Determine whether the user can create orderItems.
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
     * Determine whether the user can update the orderItem.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\OrderItem  $orderItem
     *
     * @return mixed
     */
    public function update(User $user, OrderItem $orderItem)
    {
        return $user->id === $orderItem->order->user_id;
    }

    /**
     * Determine whether the user can delete the orderItem.
     *
     * @param  \Jakten\Models\User  $user
     * @param  \Jakten\Models\OrderItem  $orderItem
     *
     * @return mixed
     */
    public function delete(User $user, OrderItem $orderItem)
    {
        return $user->id === $orderItem->order->user_id;
    }
}
