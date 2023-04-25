<?php namespace Jakten\Repositories\Contracts;

use Illuminate\Http\Request;
use Jakten\DataObjects\Bounds;
use Jakten\Models\City;
use Jakten\Models\Organization;
use Jakten\Models\Vehicle;
use Jakten\Models\VehicleSegment;

/**
 * Interface SchoolRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface SchoolRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param $slug
     *
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function bySlug($slug);

    /**
     * @param City|array|string $city
     *
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function inCity($city);

    /**
     * @param Vehicle|string|array $vehicle
     *
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function hasVehicle($vehicle);

    /**
     * @param VehicleSegment|string|array $vehicleSegment
     *
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function hasSegment($vehicleSegment);

    /**
     * @param Bounds $bounds
     *
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function withinBounds(Bounds $bounds);

    /**
     * @param array $ids
     *
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function hasId($ids);

    /**
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function isMember();

    /**
     * @param Organization $organization
     *
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function belongsTo(Organization $organization);

    /**
     * @param bool $acceptsGiftCard
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function acceptsGiftCard($acceptsGiftCard = true);

    /**
     * @param Request $request
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function search(Request $request);

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function haveBookableCourses();

    public function coursesByTypeVehicleFilter($school,$vehicleSegmentId = null);
}
