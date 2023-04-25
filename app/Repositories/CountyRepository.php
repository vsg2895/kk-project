<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\County;
use Jakten\Repositories\Contracts\CountyRepositoryContract;

/**
 * Class CountyRepository
 * @package Jakten\Repositories
 */
class CountyRepository extends BaseRepository implements CountyRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return County::class;
    }
}
