<?php namespace Jakten\Repositories\Contracts;

use Carbon\Carbon;
use Jakten\DataObjects\Bounds;
use Jakten\Models\City;
use Jakten\Models\Organization;
use Jakten\Models\School;
use Jakten\Models\Vehicle;
use Jakten\Models\VehicleSegment;

/**
 * Interface CourseRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface CourseRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param City|array|string $city
     *
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function inCity($city);

    /**
     * @param array|VehicleSegment|string $segment
     *
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function withinSegment($segment);

    /**
     * @param Vehicle|string $vehicle
     *
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function forVehicle($vehicle);

    /**
     * @param $from
     * @param $to
     *
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function isBetween(Carbon $from, Carbon $to);

    /**
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function inFuture();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function availableSeats();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function old();

    /**
     * @param Bounds $bounds
     *
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function withinBounds(Bounds $bounds);

    /**
     * @param School|array|string $school
     *
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function bySchool($school);

    /**
     * @param Organization $organization
     *
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function byOrganization(Organization $organization);

    /**
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function hasBookings();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function recentlyBooked();
}
