<?php namespace Jakten\Repositories\Contracts;

use Jakten\Models\User;

/**
 * Interface NotifyEventsRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface NotifyEventsRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param User $user
     * @return mixed
     */
    public function byRole(User $user);

    /**
     * @return \Illuminate\Database\Eloquent\Builder|NotifyEventsRepositoryContract
     */
    public function active();

}
