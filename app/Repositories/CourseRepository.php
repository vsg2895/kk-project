<?php namespace Jakten\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Jakten\DataObjects\Bounds;
use Jakten\Models\{City, Course, Organization, School, Vehicle, VehicleSegment};
use Jakten\Repositories\Contracts\CourseRepositoryContract;

/**
 * Class CourseRepository
 * @package Jakten\Repositories
 */
class CourseRepository extends BaseRepository implements CourseRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return Course::class;
    }

    /**
     * @param $ids
     * @param null $with
     * @return \Illuminate\Database\Eloquent\Collection|Model|null|static|static[]
     */
    public function getCourses($ids, $with = null){
        if($with){
            return $this->model->with($with)->find($ids);
        } else {
            return $this->model->find($ids);
        }
    }

    /**
     * @param City|array|string $city
     *
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function inCity($city)
    {
        if (is_a($city, City::class)) {
            $this->query()->where('city_id', $city->id);
        } elseif (is_array($city)) {
            $this->query()->whereIn('city_id', $city);
        } else {
            $this->query()->where('city_id', (int)$city);
        }

        return $this;
    }

    /**
     * @param array|VehicleSegment|string $segment
     *
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function withinSegment($segment)
    {
        if (is_a($segment, VehicleSegment::class)) {
            $segment = [$segment->id];
        } elseif (!is_array($segment)) {
            $segment = [$segment];
        }

        $this->query()->whereIn('vehicle_segment_id', $segment);

        return $this;
    }

    /**
     * @param Vehicle|string $vehicle
     *
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function forVehicle($vehicle)
    {
        $vehicleId = is_string($vehicle) ? $vehicle : $vehicle->id;
        $this->query()->whereHas('segment', function ($query) use ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        });

        return $this;
    }

    /**
     * @param $from
     * @param $to
     *
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function isBetween(Carbon $from, Carbon $to)
    {
        if ($from && $to) {
            $this->query()->whereBetween('start_time', [$from, $to]);
        }

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function inFuture()
    {
        $this->query()->where('start_time', '>=', Carbon::now());

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function old()
    {
        $this->query()->where('start_time', '<', Carbon::now());

        return $this;
    }

    /**
     * @param School|array|string $school
     *
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function bySchool($school)
    {
        if (is_a($school, School::class)) {
            $this->query()->where('school_id', $school->id);
        } elseif (is_array($school)) {
            $this->query()->whereIn('school_id', $school);
        } else {
            $this->query()->where('school_id', $school);
        }

        return $this;
    }

    /**
     * @param Bounds $bounds
     *
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function withinBounds(Bounds $bounds)
    {

        $this->query()->where(function ($query) use ($bounds) {
            $query->where('courses.latitude', '>=', $bounds->latitudeLow)
                ->where('courses.latitude', '<=', $bounds->latitudeHigh);
        });

        $this->query()->where(function ($query) use ($bounds) {
            $query->where('courses.longitude', '>=', $bounds->longitudeLow)
                ->where('courses.longitude', '<=', $bounds->longitudeHigh);
        });

        return $this;
    }

    /**
     * @param Organization $organization
     *
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function byOrganization(Organization $organization, $inFuture = false)
    {
        $this->query()->whereIn('school_id', $organization->schools->pluck('id')->all());

        if ($inFuture) {
            $this->query()->where('start_time', '>', Carbon::now());
        }

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function hasBookings()
    {
        $this->query()->whereHas('bookings');

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function recentlyBooked()
    {
        $this->query()->join('course_participants', 'courses.id', '=', 'course_participants.course_id')
            ->orderBy('booked', 'desc');

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|CourseRepositoryContract
     */
    public function availableSeats()
    {
        $this->query()->where('seats', '>', 0);

        return $this;
    }
}
