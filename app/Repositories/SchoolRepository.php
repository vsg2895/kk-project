<?php namespace Jakten\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Jakten\Helpers\Search;
use Illuminate\Http\Request;
use Jakten\DataObjects\Bounds;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use Jakten\Models\{City, Organization, School, Vehicle, VehicleSegment};

/**
 * Class SchoolRepository
 * @package Jakten\Repositories
 */
class SchoolRepository extends BaseRepository implements SchoolRepositoryContract
{
    /**
     * @constant Integer
     */
    const DISTANCE = 30; // kilometers

    /**
     * @constant Integer
     */
    const EARTH_RADIUS = 6371; // kilometers

    /**
     * @return Model
     */
    protected function model()
    {
        return School::class;
    }

    /**
     * @param $slug
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function bySlug($slug)
    {
        $id = substr($slug, strrpos($slug, '-') + 1);

        $this->query()->where('id', $id);

        return $this;
    }

    /**
     * @param City|array|string $city
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function inCity($city)
    {
        if (is_a($city, City::class)) {
            $this->query()->where('schools.city_id', $city->id);
        } elseif (is_array($city)) {
            $this->query()->whereIn('schools.city_id', $city);
        } else {
            $this->query()->where('schools.city_id', $city);
        }

        return $this;
    }

    /**
     * @param Vehicle|string|array $vehicle
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function hasVehicle($vehicle)
    {
        if (is_string($vehicle)) {
            $vehicle = [$vehicle];
        } elseif (is_int($vehicle)) {
            $vehicle = [(string)$vehicle];
        } elseif (is_a($vehicle, Vehicle::class)) {
            $vehicle = [$vehicle->id];
        }

        $this->query()->join('schools_vehicles', function ($join) use ($vehicle) {
            $join->on('schools_vehicles.school_id', '=', 'schools.id')
              ->where(function ($query) use ($vehicle) {
                  foreach ($vehicle as $vehicleId) {
                      $query->orWhere('schools_vehicles.vehicle_id', $vehicleId);
                  }
              });
        });

        return $this;
    }

    /**
     * @param VehicleSegment|string|array $vehicleSegment
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function hasSegment($vehicleSegment)
    {
        if (is_string($vehicleSegment)) {
            $vehicleSegment = [$vehicleSegment];
        } elseif (is_a($vehicleSegment, VehicleSegment::class)) {
            $vehicleSegment = [$vehicleSegment->id];
        }

        $this->query()->join('schools_vehicle_segments', function ($join) use ($vehicleSegment) {
            $join->on('schools_vehicle_segments.school_id', '=', 'schools.id')
              ->where(function ($query) use ($vehicleSegment) {
                  foreach ($vehicleSegment as $vehicleSegmentId) {
                      $query->orWhere('schools_vehicle_segments.vehicle_segment_id', $vehicleSegmentId);
                  }
              });
        });

        return $this;
    }

    /**
     * @param array $ids
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function hasId($ids)
    {
        if (count($ids)) {
            $this->query()->whereIn('schools.id', $ids);
        }

        return $this;
    }

    /**
     * @param Bounds $bounds
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function withinBounds(Bounds $bounds)
    {
        $this->query()->where(function ($query) use ($bounds) {
            $query->where('schools.latitude', '>=', $bounds->latitudeLow)
              ->where('schools.latitude', '<=', $bounds->latitudeHigh);
        });

        $this->query()->where(function ($query) use ($bounds) {
            $query->where('schools.longitude', '>=', $bounds->longitudeLow)
              ->where('schools.longitude', '<=', $bounds->longitudeHigh);
        });

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function isMember()
    {
        $this->query()->whereNotNull('organization_id');

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function haveBookableCourses()
    {
        $this->query()->whereHas('courses', function (Builder $query) {
            $query->where('start_time', '>', \DB::raw('NOW()'));
        });

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function haveBookableCoursesOrAddons()
    {
        $this->query()->where(function($query) {
            $query->whereHas('courses', function (Builder $query) {
                $query->where('start_time', '>', \DB::raw('NOW()'))
                    ->where('courses.seats', '>', 0)
                    ->where('courses.deleted_at', '=', null);
            })->orWhereHas('customAddons', function (Builder $query) {
                $query->where('custom_addons.active', '=', 1)
                    ->where('custom_addons.price', '>', 0);
            })->orWhereHas('addons', function (Builder $query) {
                $query->where('schools_addons.price', '>', 0);
            });
        });

        return $this;
    }

    /**
     * @param Organization $organization
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function belongsTo(Organization $organization)
    {
        $this->query()->where('organization_id', $organization->id);

        return $this;
    }

    /**
     * @param bool $acceptsGiftCard
     * @return \Illuminate\Database\Eloquent\Builder|SchoolRepositoryContract
     */
    public function acceptsGiftCard($acceptsGiftCard = true)
    {
        $this->query()->where('accepts_gift_card', $acceptsGiftCard);

        return $this;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function search(Request $request)
    {
        $qb = $this->query()
//          ->withTrashed()
          ->leftJoin('organizations', 'schools.organization_id', '=', 'organizations.id')
          ->leftjoin('users', 'users.organization_id', '=', 'organizations.id')
          ->leftjoin(
            DB::raw(
              '(SELECT `school_id`, max(`start_time`) as start_time FROM `courses` where deleted_at is null group by school_id) as `courses`'),
            'schools.id', '=', 'courses.school_id'
          );

        if ($request->has('status')) {
            $statuses = $request->get('status');
            $qb->where(function ($query) use ($statuses) {
                foreach ($statuses as $status) {
                    switch ($status) {
                        case 'coming':
                            $query->orWhere([
                              ['courses.start_time', '>=', Carbon::now()],
                              ['schools.deleted_at', '=', null]
                            ]);
                            break;
                        case 'passed':
                            $query->orWhere([
                              ['courses.start_time', '<=', Carbon::now()],
                              ['schools.deleted_at', '=', null]
                            ]);
                            break;
                        case 'none':
                            $query->orWhereNull('courses.start_time')->where('not_member', 0);
                            break;
                        case 'notMember':
                            $query->orWhere('not_member', 1);
                            break;
                        case 'connected':
                            $query->orWhere('connected_to', 1);
                            break;
                    }
                }
            });
        }
        $qb->select('schools.*')
          ->groupBy('schools.id');

        return Search::search($request->get('s'), $qb, 'schools', function ($queryBuilder, $terms) {
            $queryBuilder->where(function ($query) use ($terms) {
                foreach ($terms as $str) {
                    foreach ([
                               'schools.name',
                               'schools.address',
                               'schools.coaddress',
                               'schools.zip',
                               'schools.postal_city',
                               'schools.phone_number',
                               'schools.contact_email',
                               'schools.booking_email',
                               'schools.website',
                               'schools.description',
                                 // 'average_rating'
                             ] as $field) {
                        $query->orWhere($field, 'like', '%' . $str . '%');
                    }
                }
            });
        }, true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    public function hasCourses()
    {
        $this->query()->whereHas('customAddons', function ($query) {
            $query->where('active', 1);
        });

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    public function hasCoursesOnTheDate($date, $cityId = null, $type = null)
    {
        $this->query()->with('city')->whereHas('courses', function (Builder $query) use ($date) {
            $query->whereDate('courses.start_time', $date);
        });

        if ($cityId) {
            if ($city = City::findOrFail($cityId)) {
                $bounds = [];

                $distance = $city->search_radius ? (int) $city->search_radius : self::DISTANCE;
                $bounds[] = $city->latitude - rad2deg($distance / self::EARTH_RADIUS);
                $bounds[] = $city->longitude - rad2deg($distance / self::EARTH_RADIUS / cos(deg2rad($city->latitude)));
                $bounds[] = $city->latitude + rad2deg($distance / self::EARTH_RADIUS);
                $bounds[] = $city->longitude + rad2deg($distance / self::EARTH_RADIUS / cos(deg2rad($city->latitude)));

                $bounds = new Bounds($bounds[0], $bounds[1], $bounds[2], $bounds[3]);
                $this->withinBounds($bounds);
            }
        }

        if ($type) {
            $this->query()->whereHas('courses', function (Builder $query) use ($type) {

                if (in_array($type, [\Jakten\Models\VehicleSegment::PUBLIC_DIGITAL_COURSE_ID, \Jakten\Models\VehicleSegment::DIGITAL_COURSE_ID])) {
                    $query->whereIn('courses.vehicle_segment_id', [\Jakten\Models\VehicleSegment::PUBLIC_DIGITAL_COURSE_ID, \Jakten\Models\VehicleSegment::DIGITAL_COURSE_ID]);
                } else {
                    $query->where('courses.vehicle_segment_id', '=', $type);
                }
            });
        }

        return $this;
    }

    public function coursesByTypeVehicleFilter($school, $vehicleSegmentId = null)
    {
        $courses = $school->courses;
        if (!is_null($vehicleSegmentId)) {
            $courses = $courses->where('vehicle_segment_id', $vehicleSegmentId);
        }

        return [
            'upcomingCourses' => $courses->where('start_time', '>=', \Carbon\Carbon::now())->sortBy('start_time'),
            'completedCourses' => $courses->where('start_time', '<', \Carbon\Carbon::now())->sortByDesc('start_time')
        ];

    }
}
