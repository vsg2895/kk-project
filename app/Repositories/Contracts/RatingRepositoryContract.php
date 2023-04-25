<?php namespace Jakten\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Jakten\Models\Organization;
use Jakten\Models\School;
use Jakten\Models\User;

/**
 * Interface RatingRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface RatingRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param User $user
     *
     * @return Builder|RatingRepositoryContract
     */
    public function byUser(User $user);

    /**
     * @param School $school
     *
     * @return Builder|RatingRepositoryContract
     */
    public function ofSchool(School $school);

    /**
     * @param Organization $organization
     *
     * @return Builder|RatingRepositoryContract
     */
    public function withinOrganization(Organization $organization);
}
