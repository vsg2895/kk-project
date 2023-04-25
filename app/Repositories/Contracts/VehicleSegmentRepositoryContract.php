<?php namespace Jakten\Repositories\Contracts;

use Jakten\Models\School;
use Jakten\Models\Vehicle;
use Jakten\Models\VehicleSegment;

/**
 * Interface VehicleSegmentRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface VehicleSegmentRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param Vehicle|null $vehicle
     *
     * @return \Illuminate\Database\Eloquent\Builder|VehicleSegmentRepositoryContract
     */
    public function forVehicle(Vehicle $vehicle = null);

    /**
     * @return \Illuminate\Database\Eloquent\Builder|VehicleSegmentRepositoryContract
     */
    public function bookable();

    /**
     * @param array $data
     * @return VehicleSegment|null|false
     * @throws \Exception
     */
    public function create(array $data = []);

    /**
     * @param School $school
     * @return \Illuminate\Database\Eloquent\Builder|VehicleSegmentRepositoryContract
     */
    public function forSchool(School $school);
}
