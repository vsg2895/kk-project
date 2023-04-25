<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\Vehicle;
use Jakten\Repositories\Contracts\VehicleRepositoryContract;

/**
 * Class VehicleRepository
 * @package Jakten\Repositories
 */
class VehicleRepository extends BaseRepository implements VehicleRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return Vehicle::class;
    }
}
