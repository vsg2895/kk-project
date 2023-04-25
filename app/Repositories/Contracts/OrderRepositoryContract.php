<?php namespace Jakten\Repositories\Contracts;

use Illuminate\Http\Request;
use Jakten\Models\Organization;
use Jakten\Models\User;

/**
 * Interface OrderRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface OrderRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param User $user
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function byUser(User $user);

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function notPaid();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function paid();

    /**
     * @param string $paymentMethod
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function withPaymentMethod($paymentMethod);

    /**
     * @param Organization $organization
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function forOrganization(Organization $organization);

    /**
     * @param Request $request
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function search(Request $request);
}
