<?php namespace Jakten\Services;

use Carbon\Carbon;
use Jakten\Facades\Auth;
use Jakten\Models\City;
use Jakten\DataObjects\Bounds;
use Jakten\Helpers\Participants;
use Jakten\Exceptions\CourseFullException;
use Jakten\Exceptions\CourseAndOrderSchoolDoesNotMathException;
use Illuminate\Foundation\Http\FormRequest;
use Jakten\Models\{Course, CourseOrder, CourseParticipant, Order, OrderItem};
use Illuminate\{Http\Request,
    Support\Collection,
    Support\Facades\Artisan,
    Support\Facades\Cache,
    Support\Facades\DB,
    Validation\UnauthorizedException};
use Jakten\Repositories\Contracts\{CityRepositoryContract, CourseRepositoryContract};

/**
 * Class CourseService
 * @package Jakten\Services
 */
class CourseService
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
     *
     */
    const CUSTOM_RADIUSES = [//segment_id => [city_id => radius]
        13 => [//risk2
            38 => 40,//Halmstad
            86 => 50,//Båstad
            89 => 50,//Helsingborg
            96 => 60,//Landskrona
            97 => 60,//Lund
            98 => 65,//Malmo
            145 => 20,//Södertälje
            167 => 40,//Uppsala
            208 => 70,//Borås
        ],
        26 => [//risk1+2combo
            145 => 40,//Södertälje
            156 => 40,//Eskilstuna
        ]
    ];

    /**
     * @var CourseRepositoryContract
     */
    private $courses;

    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * @var CityRepositoryContract
     */
    private $cities;
    /**
     * @var RuleAPIService
     */
    private $ruleAPIService;

    /**
     * CourseService constructor.
     *
     * @param CourseRepositoryContract $courses
     * @param ModelService $modelService
     * @param SchoolService $schoolService
     * @param CityRepositoryContract $cities
     */
    public function __construct(
        CourseRepositoryContract $courses,
        ModelService $modelService,
        CityRepositoryContract $cities,
        SchoolService $schoolService,
        RuleAPIService $ruleAPIService
    )
    {
        $this->courses = $courses;
        $this->modelService = $modelService;
        $this->schoolService = $schoolService;
        $this->cities = $cities;
        $this->ruleAPIService = $ruleAPIService;
    }

    /**
     * @param Request $request
     *
     */
    public function storeCourseOrder(Request $request) {
        $schoolsArray = explode(',', $request->get('schools_id'));

        if (!count($schoolsArray)) {
            return;
        }

        $fields['date'] = $request->get('start_time');
        $fields['vehicle_segment'] = $request->get('type');
        $fields['city_id'] = $request->get('city');
        $fields['user_id'] = Auth::user()->id;

        DB::table('courses_order')
            ->where(DB::raw('DATE(date)'), '=', $fields['date'])
            ->where('city_id', '=', $fields['city_id'])
            ->where('vehicle_segment', '=', $fields['vehicle_segment'])
            ->delete();

        foreach (array_reverse($schoolsArray) as $key => $school) {
            $fields['school_id'] = (int)$school;
            $fields['order'] = (int)$key + 1;
            $courseOrder = $this->modelService->createModel(CourseOrder::class, $fields);
            $courseOrder->save();
        }

        if (\Jakten\Models\VehicleSegment::DIGITAL_COURSE_ID) {
            $fields['vehicle_segment'] = \Jakten\Models\VehicleSegment::PUBLIC_DIGITAL_COURSE_ID;

            DB::table('courses_order')
                ->where(DB::raw('DATE(date)'), '=', $fields['date'])
                ->where('city_id', '=', $fields['city_id'])
                ->where('vehicle_segment', '=', $fields['vehicle_segment'])
                ->delete();

            foreach (array_reverse($schoolsArray) as $key => $school) {
                $fields['school_id'] = (int)$school;
                $fields['order'] = (int)$key + 1;
                $courseOrder = $this->modelService->createModel(CourseOrder::class, $fields);
                $courseOrder->save();
            }
        }
    }

    /**
     * @param FormRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function storeCourse(FormRequest $request)
    {

        $digital = ['digital_shared' => false];
        $fields = $allFields = $request->all();

        if (in_array($request->vehicle_segment_id, \Jakten\Models\VehicleSegment::SHARED_COURSES) && Auth::user()->isAdmin()) {
            $schools = $this->schoolService->getSchoolsWhichHostDigital();
            foreach ($schools as $school) {
                if ((string)$school['id'] === (string)$request->get('school_id')) {
                    continue;
                }

                if (Course::query()
                    ->where('school_id', (string)$school['id'])
                    ->where('start_time', '=', $fields['start_time'])
                    ->where('digital_shared', '=', true)
                    ->first())
                {
                    continue;
                }

                $fields['school_id'] = $school['id'];
                $fields['address'] = 'Online';
                $fields['latitude'] = $school['latitude'];
                $fields['longitude'] = $school['longitude'];
                $fields['postal_city'] = $school['postal_city'];
                $fields['zip'] = $school['zip'];
                $fields['city_id'] = $school['city_id'];
                $fields['digital_shared'] = true;

                $courseCopy = $this->modelService->createModel(Course::class, $fields);
                $courseCopy->save();
            }

            $digital = ['digital_shared' => true];
            $allFields['address'] = 'Online';
        }

        $course = $this->modelService->createModel(Course::class, array_merge($allFields, $digital));
        $course->save();

        if ($course instanceof Course) {
            Artisan::call('kkj:course_address');
        }

        return $course;
    }

    //customize radius for specific city and segment pairs
    private function getDistance($city, $segments)
    {
        $distance = $city->search_radius ? (int) $city->search_radius : self::DISTANCE;

        $customRadiuses = self::CUSTOM_RADIUSES;

        if ($segments && $segments > 0) {
            if (strpos($segments, ',') === false) {
                $segmentId = (int)$segments;
                if (array_key_exists($segmentId, $customRadiuses)) {
                    if (array_key_exists($city->id, $customRadiuses[$segmentId])) {
                        $distance = $customRadiuses[$segmentId][$city->id];
                    }
                }
            }
        }

        return $distance;
    }

    /**
     * @param Request $request
     * @param int $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|CourseRepositoryContract[]
     */
    public function search(Request $request, int $limit = 20)
    {
        $cityId = $request->input('city_id');
        $segments = $request->input('vehicle_segments');
        $vehicleId = $request->input('vehicle_id');
        $from = $request->input('from');
        $to = $request->input('to');
        $schoolIds = $request->input('schools');
        $bounds = $request->input('bounds');
        $calendar = $request->input('calendar');
        $allCourses = (bool)$request->input('all_courses');

        $query = $this->courses->inFuture()->availableSeats();

        if (!is_null($cityId) && $cityId !== "undefined") {
            if ($cityId) {
                if (strpos($cityId, ',') !== false) {
                    $cityId = explode(',', $cityId);
                }
                // Calculating of bounding box.
                if (!empty($cityId)) {
                    if ($city = City::findOrFail($cityId)) {
                        $bounds = '';

                        $distance = $this->getDistance($city, $segments);
                        $bounds .= $city->latitude - rad2deg($distance / self::EARTH_RADIUS) . ',';
                        $bounds .= $city->longitude - rad2deg($distance / self::EARTH_RADIUS / cos(deg2rad($city->latitude))) . ',';
                        $bounds .= $city->latitude + rad2deg($distance / self::EARTH_RADIUS) . ',';
                        $bounds .= $city->longitude + rad2deg($distance / self::EARTH_RADIUS / cos(deg2rad($city->latitude)));
                    }
                }

            }
        }

        if ((!is_null($cityId) || !$allCourses) && $bounds) {
            $bounds = explode(',', $bounds);
            $bounds = new Bounds($bounds[0], $bounds[1], $bounds[2], $bounds[3]);
            $query = $query->withinBounds($bounds);

            if (!is_null($cityId)) {
                $query->getQuery()
                    ->leftJoin('courses_order', function ($join) use ($cityId) {
                        $join->on('courses_order.vehicle_segment', 'courses.vehicle_segment_id');
                        $join->on('courses_order.date', \DB::raw('DATE(courses.start_time)'));
                        $join->on('courses_order.school_id', 'courses.school_id');
                        $join->where('courses_order.city_id', '=', $cityId);
                    });
            }
        }

        $query->getQuery()->leftJoin(
            'school_algorithm_info',
            'courses.school_id',
            '=',
            'school_algorithm_info.school_id'
        );

        if ($segments && $segments > 0) {
            if (strpos($segments, ',') !== false) {
                $segments = explode(',', $segments);
            }
            if ($segments == \Jakten\Models\VehicleSegment::PUBLIC_DIGITAL_COURSE_ID) {
                $segments = [$segments, \Jakten\Models\VehicleSegment::DIGITAL_COURSE_ID];
            }
            if ($segments == \Jakten\Models\VehicleSegment::YKB_35_H) {
                $segments = array_merge([(int)$segments], \Jakten\Models\VehicleSegment::YKB);
            }

            $query = $query->withinSegment($segments);
        }

        if ($vehicleId) {
            $query = $query->forVehicle($vehicleId);
        }

        if ($from && $to && !$calendar && (int)$segments !== \Jakten\Models\VehicleSegment::ONLINE_LICENSE_THEORY) {
            $from = Carbon::createFromFormat('Y-m-d', $from);
            $to = Carbon::createFromFormat('Y-m-d', $to);

            $query = $query->isBetween($from->startOfDay(), $to->endOfDay());
        }

        if ($schoolIds) {
            if (strpos($schoolIds, ',') !== false) {
                $schoolIds = explode(',', $schoolIds);
            }

            $query = $query->bySchool($schoolIds);
        }

        if ($calendar) {
            return $query
                ->groupBy(\DB::raw('DATE(start_time)'))
                ->select(['courses.id', 'courses.vehicle_segment_id', 'courses.school_id', 'courses.length_minutes', 'courses.address', 'courses.description', 'courses.city_id', 'courses.seats', \DB::raw('DATE(start_time) as start_time'), \DB::raw('MIN(price) as price')])
                ->get();
        }

        $query = $query
            ->with('school.ratings', 'school.city', 'school.calculatedPrices', 'school.logo', 'school.organization', 'school.prices')
            ->leftJoin('schools', 'courses.school_id', 'schools.id')
            ->orderBy(\DB::raw('DATE(start_time)'))->orderBy('schools.top_partner', 'desc');

        //exceptions
        if (!is_null($cityId) && $cityId !== "undefined" && $segments) {
            if ($segments === "13" && $cityId === "145") {//risk2, Söödertälje
                $query = $query->where('schools.id', '<>', 1439);//bromma halkbana
            }
        }

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
        }

        if (is_null($cityId)) {
            if ($data) {
                $orderString .= "0 )";
                $query = $query->orderBy(DB::raw($orderString), 'desc');
            } else {
                $query = $query->orderBy('courses.price');
            }
        } else {
            $orderString .= ' IFNULL((`courses_order`.`order` * 100000000000000000000), 0)';
            if (!in_array($cityId, City::CITIES_EXCLUDE_FROM_ALGORITHM)) {
                $orderString .= '+ IF(courses.city_id = ' . $cityId . ' , 10000000000, 0)';
            }

            if ($data) {
                $orderString .= ")";
            }

            $query = $query->orderBy(DB::raw($orderString), 'desc');
        }

        return $query->select(['courses.*'])
        ->paginate($limit);

    }

    /**
     * @param Course $course
     * @param FormRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model|Course
     */
    public function updateCourse(Course $course, FormRequest $request)
    {
        if ($course->digital_shared && in_array($course->vehicle_segment_id, \Jakten\Models\VehicleSegment::SHARED_COURSES) && !Auth::user()->isAdmin()) {
            return $course;
        }

        if (in_array($course->vehicle_segment_id, \Jakten\Models\VehicleSegment::SHARED_COURSES) && Auth::user()->isAdmin()) {
            $fields = $request->all();
            unset($fields['school_id']);
            unset($fields['latitude']);
            unset($fields['longitude']);
            unset($fields['postal_city']);
            unset($fields['zip']);
            unset($fields['city_id']);

            $duplicateCourses = Course::query()
                ->where('vehicle_segment_id', '=', $course->vehicle_segment_id)
                ->where('start_time', '=', $course->start_time)
                ->get();

            foreach ($duplicateCourses as $courseDuplicate) {
                if ($course->school->id === $courseDuplicate->school->id) {
                    continue;
                }

                $courseDuplicate = $this->modelService->updateModel($courseDuplicate, $fields);
                $courseDuplicate->save();
            }
        }

        $course = $this->modelService->updateModel($course, $request->all());
        $course->save();

        if ($course instanceof Course) {
            Artisan::call('kkj:course_address');
        }

        foreach ($course->bookings as $item) {
            $item->participant->transmission = $request->get('transmission');
            $item->participant->save();
        }

        return $course;
    }

    /**
     * @param Order $order
     * @param Course $course
     * @param FormRequest $request
     * @throws CourseFullException
     * @throws CourseAndOrderSchoolDoesNotMathException
     * @throws \KlarnaException
     */
    public function addParticipants(Order $order, Course $course, FormRequest $request, $benefitId, $orderUser)
    {
        $students = new Collection($request->input('students', []));
        $tutors = new Collection($request->input('tutors', []));

        if ($order->school_id !== $course->school_id) {

            /** @var KKJTelegramBotService $kkjBot */
            $kkjBot = resolve(KKJTelegramBotService::class);
            $kkjBot->log("add_participants_{$order->id}_failed", compact('order', 'students', 'tutors'));

        }

        $students = $students->filter(function ($student) use ($course) {
            return $course->id === $student['courseId'];
        });

        $tutors = $tutors->filter(function ($tutor) use ($course) {
            return $course->id === $tutor['courseId'];
        });

        if ($course->seats < ($students->count() + $tutors->count())) {

            /** @var \Jakten\Services\Payment\Klarna\KlarnaService $klarnaService */
            $klarnaService = resolve(\Jakten\Services\Payment\Klarna\KlarnaService::class);
            $klarnaService->cancelOrder($order, true);

            throw new CourseFullException();
        }

        $students->each(function ($student) use ($order, $course, $benefitId, $orderUser) {
            $this->addParticipant($student, Participants::PARTICIPANT_STUDENT, $order, $course, $orderUser, $benefitId);
        });

        $tutors->each(function ($student) use ($order, $course, $orderUser) {
            $this->addParticipant($student, Participants::PARTICIPANT_TUTOR, $order, $course, $orderUser);
        });
    }

    /**
     * @param $participantData
     * @param $type
     * @param Order $order
     * @param Course $course
     */
    private function addParticipant($participantData, $type, Order $order, Course $course, $payer, $benefitId = null)
    {
        $courseBooking = $this->modelService->createModel(OrderItem::class, [
            'external_id' => isset($participantData['id']) ? $participantData['id'] : null,
            'amount' => $course->price,
            'quantity' => 1,
            'type' => $course->segment->name,
            'course_id' => $course->id,
            'school_id' => $course->school_id,
            'provision' => config('fees.provision'),
            'order_id' => $order->id,
            'comment' => $type,
            'benefit_id' => $benefitId,
        ]);

        $courseBooking->save();

        $participant = $this->modelService->createModel(CourseParticipant::class, $participantData);
        $participant->type = $type;
        $participant->booking()->associate($courseBooking);
        $participant->course()->associate($course);
        $participant->save();

        if (config('app.env') === 'production') {
            $this->ruleAPIService->addSubscriber($participant, 'student', $payer);
        }
    }

    /**
     * @param int $type
     * @param $startTime
     */
    public function getBy(int $type, $startTime)
    {
        return $this->courses->query()
            ->where('vehicle_segment_id', '=', $type)
            ->where('start_time', '=', $startTime)->get();
    }

    /**
     * @param Course $course
     * @throws \Exception
     */
    public function delete(Course $course)
    {
        if ($course->bookings->where('cancelled', false)->count()) {
            throw new UnauthorizedException('You cant delete a course with bookings');
        }

        $course->delete();
    }
}
