<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\CityInfo;
use Jakten\Repositories\Contracts\CityInfoRepositoryContract;

/**
 * Class CityInfoRepository
 * @package Jakten\Repositories
 */
class CityInfoRepository extends BaseRepository implements CityInfoRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return CityInfo::class;
    }
}
