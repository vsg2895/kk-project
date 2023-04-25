<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\{School, SchoolSegmentPrice};
use Jakten\Repositories\Contracts\SchoolSegmentPriceRepositoryContract;

/**
 * Class SchoolSegmentPriceRepository
 * @package Jakten\Repositories
 */
class SchoolSegmentPriceRepository extends BaseRepository implements SchoolSegmentPriceRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return SchoolSegmentPrice::class;
    }

    /**
     * @param School $school
     *
     * @return \Illuminate\Database\Eloquent\Builder|SchoolSegmentPriceRepositoryContract
     */
    public function forSchool(School $school)
    {
        $this->query()->where('school_id', $school->id);

        return $this;
    }

    /**
     * @param $type
     *
     * @return \Illuminate\Database\Eloquent\Builder|SchoolSegmentPriceRepositoryContract
     */
    public function forType($type)
    {
        $this->query()->where('type', $type);

        return $this;
    }
}
