<?php namespace Jakten\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Jakten\Events\NewReview;
use Jakten\Facades\Auth;
use Illuminate\Http\Request;
use Jakten\DataObjects\Bounds;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Admin\Http\Requests\UpdateSchoolDetailRequest;
use Jakten\Models\{Organization,
    School,
    City,
    SchoolClaim,
    SchoolRating,
    SchoolSegmentPrice,
    CustomAddon,
    SchoolVehicleSegment,
    User,
    Course};
use Jakten\Repositories\Contracts\{CityRepositoryContract, SchoolClaimRepositoryContract, SchoolRepositoryContract};
use Jakten\Services\Asset\{AssetService, Strategy\ImageLogo};

/**
 * Class SchoolService
 *
 * @package Jakten\Services
 */
class SchoolService
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
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * @var CityRepositoryContract
     */
    private $cities;

    /**
     * @var SchoolClaimRepositoryContract
     */
    private $schoolClaims;

    /**
     * @var AssetService
     */
    private $assetService;
    /**
     * @var RuleAPIService
     */
    private $ruleAPIService;


    /**
     * SchoolService constructor.
     *
     * @param SchoolRepositoryContract $schools
     * @param ModelService $modelService
     * @param CityRepositoryContract $cities
     * @param SchoolClaimRepositoryContract $schoolClaims
     * @param AssetService $assetService
     */
    public function __construct(
        SchoolRepositoryContract $schools,
        ModelService $modelService,
        CityRepositoryContract $cities,
        SchoolClaimRepositoryContract $schoolClaims,
        AssetService $assetService,
        RuleAPIService $ruleAPIService
    )
    {
        $this->schools = $schools;
        $this->modelService = $modelService;
        $this->cities = $cities;
        $this->schoolClaims = $schoolClaims;
        $this->assetService = $assetService;
        $this->ruleAPIService = $ruleAPIService;
    }

    /**
     * @param Request $request
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search(Request $request, $limit = 20)
    {
        $cityId = (int)$request->input('city_id');
        $isMember = $request->input('is_member', false);
        $vehicleId = $request->input('vehicle_id', 1);
        $schoolIds = $request->input('schools');
        $sortBy = $request->input('sort', 'RATING');
        $bounds = $request->input('bounds');

        if (str_contains($sortBy, 'PRICE') && !$schoolIds) {
            $sortBy = null;
        }

        $all = $request->input('all');

        $acceptsGiftCardConstraint = !!$request->input('accepts_gift_card', null);

        $query = $this->schools;
//        $query = $this->schools->where('vehicle_segment_id', '<>', 32);

        if ($cityId && !$schoolIds) {
            if (strpos($cityId, ',') !== false) {
                $cityId = explode(',', $cityId);
            }
            // Calculating of bounding box.
            if (!empty($cityId)) {
                if ($city = City::findOrFail($cityId)) {
                    $bounds = '';

                    $distance = $city->search_radius ? (int) $city->search_radius : self::DISTANCE;
                    $bounds .= $city->latitude - rad2deg($distance / self::EARTH_RADIUS) . ',';
                    $bounds .= $city->longitude - rad2deg($distance / self::EARTH_RADIUS / cos(deg2rad($city->latitude))) . ',';
                    $bounds .= $city->latitude + rad2deg($distance / self::EARTH_RADIUS) . ',';
                    $bounds .= $city->longitude + rad2deg($distance / self::EARTH_RADIUS / cos(deg2rad($city->latitude)));
                }
            }
        }

        if ($bounds && !$schoolIds) {
            $bounds = explode(',', $bounds);
            $bounds = new Bounds($bounds[0], $bounds[1], $bounds[2], $bounds[3]);
            $query = $query->withinBounds($bounds);

            if (!$query->count() && $city = City::findOrFail($cityId)) {//if there is no results with city, search by county
                $bounds = '';
                $county = $city->county;

                $distance = self::DISTANCE;
                $bounds .= $county->lat - rad2deg($distance / self::EARTH_RADIUS) . ',';
                $bounds .= $county->lng - rad2deg($distance / self::EARTH_RADIUS / cos(deg2rad($county->lat))) . ',';
                $bounds .= $county->lat + rad2deg($distance / self::EARTH_RADIUS) . ',';
                $bounds .= $county->lng + rad2deg($distance / self::EARTH_RADIUS / cos(deg2rad($county->lat)));
                $bounds = new Bounds($bounds[0], $bounds[1], $bounds[2], $bounds[3]);
                $query = $query->withinBounds($bounds);
            }
        }

        if ($isMember) {
//            $query = $query->isMember();
            $query = $query->haveBookableCourses();
        }

        if ($all) {
            $query->query()->with('customAddons');
        } else {
            $query = $query->haveBookableCourses();
            $query->query()
                ->whereHas('customAddons', function ($query) {
                    $query->whereActive(1);
                })->with([
                    'customAddons' => function ($query) {
                        $query->whereActive(1);
                    },
                ]);
        }

        $query = $query->hasVehicle($vehicleId);

        if ($schoolIds) {
            if (strpos($schoolIds, ',') !== false) {
                $schoolIds = explode(',', $schoolIds);
            } else {
                $schoolIds = [$schoolIds];
            }
            $query = $query->hasId($schoolIds);
        } elseif (!$sortBy) {
            $query->getQuery()->leftJoin(
                'school_algorithm_info',
                'schools.id',
                '=',
                'school_algorithm_info.school_id'
            );
        }

        if ($acceptsGiftCardConstraint) {
            $query->acceptsGiftCard($acceptsGiftCardConstraint);
        }

        $query = $query->query()
            ->join('school_calculated_prices', 'schools.id', '=', 'school_calculated_prices.school_id')
            ->leftJoin('courses', function($join)
            {
                $join->on('schools.id', '=', 'courses.school_id');
                $join->where('courses.start_time', '>', Carbon::now())
                    ->where('courses.seats', '>', 0)
                    ->where('courses.deleted_at', '=', null)
                    ->where('courses.vehicle_segment_id', '<>', 32);
            })
            ->leftJoin('vehicle_segments', 'courses.vehicle_segment_id', '=', 'vehicle_segments.id')
            ->leftJoin('schools_addons', 'schools.id', '=', 'schools_addons.school_id')
            ->leftJoin('custom_addons', 'schools.id', '=', 'custom_addons.school_id')
            ->where('school_calculated_prices.vehicle_id', $vehicleId)
            ->where(function($query) use ($vehicleId) {
                $query->where(function($queryBuilder) use ($vehicleId) {
                    $queryBuilder->where('courses.start_time', '>',Carbon::now())
                        ->where('vehicle_segments.vehicle_id', '=', $vehicleId)
                        ->where('courses.seats', '>', 0)
                        ->where('courses.deleted_at', '=', null);
                })
                ->orWhere(function($queryBuilder) {
                    $queryBuilder
                        ->where('schools_addons.price', '>', 0)
                        ->orWhere(function($queryBuilder) {
                            $queryBuilder->where('custom_addons.active', '=', 1)
                                ->where('custom_addons.price', '>', 0);
                        });
                });
            })
            ->where('schools.id', '!=', 1318)
            ->where('schools.id', '!=', 1458);

        if ($request->in_paket) {
            $query = $query->whereHas('addons');
        }

        if ($sortBy) {
            $query = $query->orderBy('has_future_course', 'desc');
            $query = $query->orderBy('top_partner', 'desc');
            $query = $query->orderBy('course_min_price');
        }

        if ($sortBy == 'RATING') {
            $query = $query->orderBy('average_rating', 'desc');
            $query = $query->orderBy('school_calculated_prices.prices_set', 'desc');
            $query = $query->orderBy('school_calculated_prices.comparison_price');
        } elseif ($sortBy == 'COMPARISON') {
            $query = $query->orderBy('school_calculated_prices.prices_set', 'desc');
            $query = $query->orderBy('school_calculated_prices.comparison_price');
            $query = $query->orderBy('average_rating', 'desc');
        } elseif (str_contains($sortBy, 'PRICE')) {
            $query = $query->orderBy('school_calculated_prices.prices_set', 'desc');
            $query = $query->orderBy('school_calculated_prices.comparison_price');
        } elseif (str_contains($sortBy, 'MEMBER')) {
            $query = $query->orderBy('average_rating', 'desc');
        } elseif (str_contains($sortBy, 'DRIVING_LESSON')) {
            $query = $query->orderBy('school_calculated_prices.DRIVING_LESSON');
            $query = $query->orderBy('average_rating', 'desc');
            $query = $query->where('courses.start_time', '>', Carbon::now());
        } elseif (!$sortBy && !$schoolIds) {
            $cacheKey = 'school_algorithm_info';
            if (Cache::has($cacheKey)) {
                $data = Cache::get($cacheKey);
            } else {
                $data = DB::table('search_algorithm_config')
                    ->select('*')
                    ->latest('created_at')
                    ->first();
                Cache::put($cacheKey, $data);
            }

            if ($data) {
                $orderString = '(';

                foreach ($data as $key => $val) {

                    if (in_array($key, ['user_id', 'created_at', 'avg_price'])) {
                        continue;
                    }

                    $numOfZero = 1;
                    $zeros = (int)($val/10);
                    for ($i = 1; $i <= $zeros; $i++) {
                        $numOfZero *= 10;
                    }

                    $orderString .= "IFNULL((`school_algorithm_info`.`{$key}` * {$numOfZero}), 0) + ";

                }
                $orderString = rtrim($orderString, "+ ");
            }

            if (!in_array($cityId, City::CITIES_EXCLUDE_FROM_ALGORITHM)) {
                $orderString .= '+ IF(courses.city_id = ' . $cityId . ' , 10000000000, 0)';
            }

            if ($data) {
                $orderString .= ")";
            }

            $query = $query->orderBy('top_partner', 'desc');
            $query = $query->orderBy(DB::raw($orderString), 'desc');
        } else {
            $query = $query->join('school_segment_prices', function ($join) {
                $join->on('schools.id', '=', 'school_segment_prices.school_id');
            });
            $query = $query->where('vehicle_segments.name', $sortBy)
                ->whereNotNull('school_segment_prices.amount');

            $query = $query->orderBy('school_segment_prices.amount', 'asc');
        }

        return $query
            ->select([
                'schools.*',
                DB::raw("IF(courses.start_time > NOW(), MIN(courses.price), 0) AS course_min_price"),
                DB::raw("IF(courses.start_time > NOW(), 1, 0) AS has_future_course"),
                DB::raw('MIN(schools_addons.price) AS sa_min_price'),
                DB::raw('MIN(custom_addons.price) AS ca_min_price'),
                'comparison_price',
                'DRIVING_LESSON',
                'RISK_ONE',
                'RISK_TWO',
            ])
            ->groupBy('schools.id')
            ->with(['ratings', 'usps', 'city', 'courses', 'prices', 'organization.logo', 'logo', 'addons'])
            ->paginate($limit);
    }

    /**
     * @param FormRequest $request
     * @param Organization $organization
     * @return School
     */
    public function storeSchool(FormRequest $request, Organization $organization = null)
    {
        /** @var School $school */
        $school = $this->modelService->createModel(School::class, $request->all());
        if ($organization) {
            $school->organization_id = $organization->id;
            $school->accepts_gift_card = $organization->gift_card_default;
        }

        if ($request->has('email')) {
            $school->contact_email = $request->input('email');
        }

        $school->save();

        if ($request->has('vehicles')) {
            $this->updateVehicles($school, $request->get('vehicles'));
        }

        //add school to rule
        if (config('app.env') === 'production') {
            $this->ruleAPIService->addSubscriber($school);
        }

        return $school;
    }

    /**
     * @param School $school
     * @param FormRequest $request
     * @param bool $organizationIdDeletable
     * @return \Illuminate\Database\Eloquent\Model|School
     */
    public function updateSchool(School $school, FormRequest $request, $organizationIdDeletable = false)
    {
        $user = Auth::user();
        $logo = $request->file('logo');
        $data = $request->except(['logo']);

        if ($organizationIdDeletable && $user->isAdmin() && !isset($data['organization_id']) && $data['organization_id'] == -1) {
            $data['organization_id'] = null;
        }

        /** @var School $school */
        $school = tap($school)->update($data);
        if ($school->organization_id) {
            $this->deleteClaims($school);
        }

        $addons = $request->input('addons');
        $customAddons = $request->input('customAddons');
        $prices = $request->input('prices');
        $vehicles = $request->input('vehicles');

        if (!is_null($addons)) {
            $this->updateAddons($school, $addons);
        }

        if (!is_null($customAddons)) {
            $this->updateCustomAddons($school, $customAddons);
        }

        $isPricesUpdated = false;

        if (!is_null($prices)) {
            $isPricesUpdated = $this->updatePrices($school, $prices);
        }

        if (!is_null($vehicles)) {
            $this->updateVehicles($school, $vehicles);
        }

        if ($logo) {
            $img = $this->assetService->storeImage(new ImageLogo($logo));
            $school->logo_id = $img->id;
        }

        if (isset($data['city_id'])) {
            $school->accepts_gift_card = isset($data['accepts_gift_card']);

            $school->not_member = isset($data['not_member']);

            $school->top_deal = isset($data['top_deal']);

            $school->show_left_seats = isset($data['show_left_seats']);
        }

        if(isset($data['host_digital']) && $data['host_digital']) {
            $courses = Course::query()
                ->whereIn('vehicle_segment_id', \Jakten\Models\VehicleSegment::SHARED_COURSES)
                ->where('start_time', '>=', Carbon::now())
                ->where('digital_shared', '=', true)
                ->groupBy('start_time')
                ->get();

            foreach ($courses as $course) {

                if (Course::query()
                    ->where('school_id', (string)$school->id)
                    ->where('start_time', '=', $course->start_time)
                    ->where('digital_shared', '=', true)
                    ->first())
                {
                    continue;
                }

                $dataCourse = [
                    'school_id' => (string)$school->id,
                    'vehicle_segment_id' => (string)$course->vehicle_segment_id,
                    'city_id' => (string)$school->city_id,
                    'start_time' => $course->start_time->format('Y-m-d H:i'),
                    'length_minutes' => (string)$course->length_minutes,
                    'price' => $course->price,
                    'address' => $course->address,
                    'transmission' => $course->transmission,

                    'address_description' => $course->address_description,
                    'description' => $course->description,
                    'confirmation_text' => $course->confirmation_text,
                    'seats' => (string)$course->seats,
                    'created_at' => (string)$course->created_at->toDateTimeString(),

                    'latitude' => (string)$school->latitude,
                    'longitude' => (string)$school->longitude,
                    'zip' => (string)$school->zip,

                    'postal_city' => (string)$school->postal_city,
                    'digital_shared' => true
                ];

                $courseModel = $this->modelService->createModel(Course::class, $dataCourse);
                $courseModel->save();
            }
        }

        // on the prices page there is no name field in the form
        if ($user->isAdmin() && isset($data['name'])) {
            $school->host_digital = isset($data['host_digital']);
            $school->top_partner = isset($data['top_partner']);

            $connectedFieldUpdated = isset($data['connected_to']) && !$school->connected_to;
            $school->connected_to = isset($data['connected_to']);
            if ($connectedFieldUpdated) $school->connected_at = now();
            elseif (!isset($data['connected_to'])) $school->connected_at = null;
        }
        $school->save();

        if (config('app.env') === 'production') {
            $this->ruleAPIService->addSubscriber($school);//update
        }

        if ($user->isAdmin() && isset($data['name'])) {
            if (!isset($data['top_partner'])) {
                $this->ruleAPIService->deleteTag($school->contact_email, 'top-partner');
            }
            if (!isset($data['connected_to'])) {
                $this->ruleAPIService->deleteTag($school->contact_email, 'connected');
            }
        }

        return $school;
    }

    /**
     * @param School $school
     * @throws \Exception
     */
    public function delete(School $school)
    {
        $school->delete();
    }

    /**
     * @return array
     */
    public function getSchoolsWhichHostDigital(): array
    {
        return School::query()->where('host_digital', true)->get()->toArray() ?? [];
    }

    /**
     * @param School $school
     * @return array|\Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function findSchoolRatingByUser(School $school)
    {
        $user = Auth::user();

        return $user->ratings()->where('school_id', $school->id)->first() ?? $this->averageRating($school);
    }

    /**
     * Get average rating
     *
     * @param School $school
     * @return array
     */
    public function averageRating(School $school)
    {
        return [
            'school_id' => $school->id,
            'rating' => $school->formatter()->averageRating(5),
        ];
    }

    /**
     * @param School $school
     * @return bool
     */
    public function deleteRateForSchool(School $school)
    {
        $user = Auth::user();

        $schoolRating = $user->ratings()->where('school_id', $school->id)->firstOrFail();
        $schoolRating->delete();

        return true;
    }

    /**
     * @param School $school
     * @param FormRequest $request
     * @return \Illuminate\Database\Eloquent\Model|mixed|static
     */
    public function rateSchool(School $school, FormRequest $request)
    {
        $user = Auth::user();

        $schoolRating = $user->ratings()->where('school_id', $school->id)->firstOr(function () use (
            $request,
            $school,
            $user
        ) {
            $schoolRating = $this->modelService->createModel(SchoolRating::class, $request->all());
            $schoolRating->school_id = $school->id;
            $schoolRating->user_id = $user->id;
            $schoolRating->title = $request->request->get('title');
            $schoolRating->content = $request->request->get('content');

            return $schoolRating;
        });

        $schoolRating->rating = $request->input('rating');
        $schoolRating->save();

        return $schoolRating;
    }

    /**
     * @param School $school
     * @param FormRequest $request
     * @return \Illuminate\Database\Eloquent\Model|mixed|static
     */
    public function rateCourse(School $school, Course $course, User $user, Request $request)
    {
        $schoolRating = $school->allRatings()->where('user_id', '=', $user->id)->where('course_id','=', $course->id)->firstOr(function () use (
            $request,
            $school,
            $course,
            $user
        ) {
            $schoolRating = $this->modelService->createModel(SchoolRating::class, []);
            $schoolRating->school_id = $school->id;
            $schoolRating->user_id =  $user->id;
            $schoolRating->course_id = $course->id;
            $schoolRating->title = $request->request->get('title', trans('vehicle_segments.' . strtolower($course->segment->name)));
            $schoolRating->content = $request->request->get('content', '');

            return $schoolRating;
        });

        $shouldNotify = false;

        if (!$schoolRating->id) {
            $shouldNotify = true;
        }

        $schoolRating->content = $request->request->get('content', '');
        $schoolRating->rating = $request->request->get('rating');

        $schoolRating->save();

        if ($shouldNotify) {
            event(new NewReview($school, $course, $user, $schoolRating));
        }

        return $schoolRating;
    }

    /**
     * @param School $school
     * @param array $addons
     */
    public function updateAddons(School $school, array $addons)
    {
        DB::table('schools_addons')->where('school_id', $school->id)->delete();
        foreach ($addons as $addon) {
            if (isset($addon['id'])) {
                DB::table('schools_addons')->insert([
                    'school_id' => $school->id,
                    'price' => $addon['price'],
                    'addon_id' => $addon['id'],
                    'description' => $addon['description'],
                    'top_deal' => isset($addon['top_deal']),
                    'show_left_seats' => isset($addon['show_left_seats']),
                    'left_seats' => $addon['left_seats'] ?? 0,
                ]);
            }
        }
    }

    /**
     * @param School $school
     * @param array $addons
     */
    public function updateAddonsFee(School $school, array $addons)
    {
        foreach ($addons as $addon) {
            if (isset($addon['id'])) {
                DB::table('schools_addons')
                    ->where('addon_id', $addon['id'])
                    ->where('school_id', $school->id)
                    ->update(['fee' => $addon['fee']]);
            } else {
                DB::table('schools_addons')->insert([
                    'school_id' => $school->id,
                    'price' => $addon['price']?? 0,
                    'addon_id' => $addon['id'],
                    'description' => $addon['description'] ?? '',
                    'top_deal' => isset($addon['top_deal']) ?? 0,
                    'show_left_seats' => isset($addon['show_left_seats']) ?? 0,
                    'left_seats' => $addon['left_seats'] ?? 0,
                    'fee' => $addon['fee'] ?? 0,
                ]);
            }
        }
    }

    /**
     * @param School $school
     * @param array $customAddons
     */
    public function updateCustomAddons(School $school, array $customAddons)
    {
        CustomAddon::where('school_id', $school->id)->update(['active' => false]);
        foreach ($customAddons as $addon) {
            if (isset($addon['id']) && !empty($addon['id'])) {
                if ($row = CustomAddon::find($addon['id'])) {
                    $row->active = true;
                    $row->price = $addon['price'];
                    $row->description = $addon['description'];
                    $row->top_deal = isset($addon['top_deal']);
                    $row->show_left_seats = isset($addon['show_left_seats']);
                    $row->left_seats = $addon['left_seats'] ?? 0;
                    $row->save();
                }
            }
        }
    }

    /**
     * @param School $school
     * @param array $customAddons
     */
    public function updateCustomAddonsFee(array $customAddons)
    {
        foreach ($customAddons as $addon) {
            if (isset($addon['id']) && !empty($addon['id'])) {
                if ($row = CustomAddon::find($addon['id'])) {
                    $row->fee = $addon['fee'] ?? 0;
                    $row->save();
                }
            }
        }
    }

    /**
     * @param School $school
     * @param Request $request
     * @return bool
     */
    public function updateFees(School $school, Request $request)
    {
        $addons = $request->input('addons');
        $customAddons = $request->input('customAddons');
        $prices = $request->input('fees');

        if (!is_null($addons)) {
            $this->updateAddonsFee($school, $addons);
        }

        if (!is_null($customAddons)) {
            $this->updateCustomAddonsFee($customAddons);
        }

        $isPricesUpdated = false;

        if (!is_null($prices)) {
            $isPricesUpdated = $this->updatePricesFee($school, $prices);
        }
    }


    /**
     * @param School $school
     * @param array $formPrices
     * @return bool
     */
    public function updatePrices(School $school, array $formPrices)
    {
        $updated = false;
        $formPrices = collect($formPrices);
        $storedPrices = SchoolSegmentPrice::where('school_id', $school->id)
            ->whereIn('id', $formPrices->keys()->all())
            ->get();

        foreach ($formPrices as $id => $data) {
            $price = $storedPrices->first(function ($value) use ($id) {
                return ($value->id == $id && $value->segment->editable);
            });
            if ($price->amount != $data['amount']) {
                $updated = true;
            }
            if (is_null($data['amount'])) {
                $dp = $price->segment->default_price;
                $price->amount = $dp ? $price->segment->default_price : null;
                $price->comment = $dp ? 'Priset är baserat på en schablon' : null;
                $price->quantity = $dp ? 1 : null;
            } else {
                $price->amount = $data['amount'];
                $price->quantity = isset($data['quantity']) ? $data['quantity'] : 1;
                $price->comment = isset($data['comment']) ? $data['comment'] : null;
            }
            $price->save();
        }

        return $updated;
    }

    /**
     * @param School $school
     * @param array $formPrices
     * @return bool
     */
    public function updatePricesFee(School $school, array $formPrices)
    {
        $updated = false;
        $formPrices = collect($formPrices);
        $storedPrices = SchoolSegmentPrice::where('school_id', $school->id)
            ->whereIn('id', $formPrices->keys()->all())
            ->get();
        foreach ($formPrices as $id => $data) {
            $price = $storedPrices->first(function ($value) use ($id) {
                return ($value->id == $id);
            });

            if (!is_null($data['fee'])) {
                $price->fee = $data['fee'] ?? 0;
            }
            $price->save();
        }

        return $updated;
    }

    /**
     * @param School $school
     * @param User $user
     */
    public function claim(School $school, User $user)
    {
        if ($user->isOrganizationUser()) {
            $organization = $user->organization;
            if (!$this->schoolClaims->byOrganization($organization)->ofSchool($school)->query()->exists()) {
                $claim = new SchoolClaim();
                $claim->organization_id = $organization->id;
                $claim->school_id = $school->id;
                $claim->user_id = $user->id;
                $claim->save();
            }
        }
    }

    /**
     * @param School $school
     */
    public function deleteClaims(School $school)
    {
        $this->schoolClaims->query()->where('school_id', $school->id)->delete();
    }

    /**
     * @param School $school
     * @param array $vehicles
     */
    private function updateVehicles(School $school, array $vehicles)
    {
        $school->availableVehicles()->detach();
        $school->availableVehicles()->attach($vehicles);
    }

    /**
     * Search for schools that offer gift card
     *
     * @param int $cityId : id for city
     * @return SchoolRepositoryContract
     */
    public function getGiftCardSchools(int $cityId)
    {
        $query = $this->schools;
        if ($cityId > 0) {
            $query->inCity($cityId);
        }
        //if ($cityId == 0)
        $query->acceptsGiftCard(true);

        $query->haveBookableCoursesOrAddons();

        $query->with('city');

        return $query;
    }
}
