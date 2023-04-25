<?php namespace Jakten\Repositories\Contracts;

use Jakten\Models\Organization;
use Jakten\Models\School;
use Jakten\Models\User;

/**
 * Interface OrderItemRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface OrderItemRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param School $school
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function forSchool(School $school);

    /**
     * @param Organization $organization
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function forOrganization(Organization $organization);

    /**
     * @param User $user
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function byUser(User $user);

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function isCourseBooking();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function active();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function notDelivered();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function delivered();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function getCoursesSinceYesterday();
    
    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function getCoursesSinceTomorrow();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function isGiftCard();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function hasExternalId();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function getTotalAmountBySchoolCurrentYear($schoolId, $startMonth);

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function getTotalAmountBySchoolPrevYear($schoolId, $startMonth);
}
