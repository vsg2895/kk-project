<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Jakten\Models\{Organization, School, SchoolRating, User};
use Jakten\Repositories\Contracts\RatingRepositoryContract;

/**
 * Class RatingRepository
 * @package Jakten\Repositories
 */
class RatingRepository extends BaseRepository implements RatingRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return SchoolRating::class;
    }

    /**
     * @param User $user
     *
     * @return Builder|RatingRepositoryContract
     */
    public function byUser(User $user)
    {
        $this->query()->where('user_id', $user->id);

        return $this;
    }

    /**
     * @param School $school
     *
     * @return Builder|RatingRepositoryContract
     */
    public function ofSchool(School $school)
    {
        $this->query()->where('school_id', $school->id);

        return $this;
    }

    /**
     * @param Organization $organization
     *
     * @return Builder|RatingRepositoryContract
     */
    public function withinOrganization(Organization $organization)
    {
        $this->query()->whereIn('school_id', $organization->schools->pluck('id')->all());

        return $this;
    }
}
