<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\{Builder, Model};
use Jakten\Models\{Organization, School, SchoolClaim};
use Jakten\Repositories\Contracts\SchoolClaimRepositoryContract;

/**
 * Class SchoolClaimRepository
 * @package Jakten\Repositories
 */
class SchoolClaimRepository extends BaseRepository implements SchoolClaimRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return SchoolClaim::class;
    }

    /**
     * @param Organization $organization
     *
     * @return $this|Builder
     */
    public function byOrganization(Organization $organization)
    {
        $this->query()->where('organization_id', $organization->id);

        return $this;
    }

    /**
     * @param School $school
     *
     * @return $this|Builder
     */
    public function ofSchool(School $school)
    {
        $this->query()->where('school_id', $school->id);

        return $this;
    }
}
