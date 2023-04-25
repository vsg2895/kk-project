<?php namespace Jakten\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Jakten\Models\Organization;
use Jakten\Models\School;

/**
 * Interface SchoolClaimRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface SchoolClaimRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param Organization $organization
     *
     * @return $this|Builder
     */
    public function byOrganization(Organization $organization);

    /**
     * @param School $school
     *
     * @return $this|Builder
     */
    public function ofSchool(School $school);
}
