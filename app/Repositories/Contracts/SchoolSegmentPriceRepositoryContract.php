<?php namespace Jakten\Repositories\Contracts;

use Jakten\Models\School;

/**
 * Interface SchoolSegmentPriceRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface SchoolSegmentPriceRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param School $school
     *
     * @return \Illuminate\Database\Eloquent\Builder|SchoolSegmentPriceRepositoryContract
     */
    public function forSchool(School $school);

    /**
     * @param $type
     *
     * @return \Illuminate\Database\Eloquent\Builder|SchoolSegmentPriceRepositoryContract
     */
    public function forType($type);
}
