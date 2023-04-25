<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\SchoolUsps;
use Jakten\Repositories\Contracts\UspsRepositoryContract;

/**
 * Class UspsRepository
 * @package Jakten\Repositories
 */
class UspsRepository extends BaseRepository implements UspsRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return SchoolUsps::class;
    }

    public function getSchoolId()
    {
        return $this->query()->firstOrFail()->school_id;
    }

    /**
     * @param $ids
     * @return \Jakten\Repositories\Contracts\UspsRepositoryContract;
     */
    public function hasId($ids)
    {
        if (count($ids)) {
            $this->query()->whereIn('school_id', $ids);
        }

        return $this;
    }
}
