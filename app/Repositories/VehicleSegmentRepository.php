<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Jakten\Models\{School, Vehicle, VehicleSegment};
use Jakten\Repositories\Contracts\VehicleSegmentRepositoryContract;

/**
 * Class VehicleSegmentRepository
 * @package Jakten\Repositories
 */
class VehicleSegmentRepository extends BaseRepository implements VehicleSegmentRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return VehicleSegment::class;
    }

    /**
     * @param Vehicle|null $vehicle
     *
     * @return \Illuminate\Database\Eloquent\Builder|VehicleSegmentRepositoryContract
     */
    public function forVehicle(Vehicle $vehicle = null)
    {
        if ($vehicle) {
            $this->query()->where('vehicle_id', $vehicle->id);
        }

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|VehicleSegmentRepositoryContract
     */
    public function bookable()
    {
        $this->query()->where('bookable', true);

        return $this;
    }

    /**
     * @param School $school
     * @return \Illuminate\Database\Eloquent\Builder|VehicleSegmentRepositoryContract
     */
    public function forSchool(School $school)
    {
        $this->query()->join('schools_vehicles', 'vehicle_segments.vehicle_id', '=', 'schools_vehicles.vehicle_id')
            ->where('schools_vehicles.school_id', $school->id);

        return $this;
    }

    /**
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder|VehicleSegmentRepositoryContract
     */
    public function forDate(string $date, $cityId = null)
    {
        $this->query()->whereHas('courses', function (Builder $query) use ($date) {
            $query->whereDate('courses.start_time', $date);
        });

        if ($cityId) {
            $this->query()->whereHas('courses', function (Builder $query) use ($cityId) {
                $query->where('courses.city_id', '=', $cityId);
                $query->where('courses.vehicle_segment_id', '!=', \Jakten\Models\VehicleSegment::PUBLIC_DIGITAL_COURSE_ID);
            });
        }

        return $this;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data = [])
    {
        return $this->query()->create($data);
    }
}
