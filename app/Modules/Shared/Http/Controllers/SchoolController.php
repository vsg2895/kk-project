<?php namespace Shared\Http\Controllers;

use Illuminate\Http\Request;
use Jakten\Models\Course;
use Jakten\Models\PendingExternalOrder;
use Jakten\Repositories\Contracts\{CityRepositoryContract, SchoolRepositoryContract};
use Jakten\Models\User;
use Jakten\Services\{KKJTelegramBotService, Payment\Klarna\KlarnaService, Schema\LocalBusinessService, SchoolService};

/**
 * Class SchoolController
 *
 * @package Shared\Http\Controllers
 */
class SchoolController extends Controller
{
    /**
     * @constant RESULT_TYPE_SCHOOL
     */
    const RESULT_TYPE_SCHOOL = 'SCHOOL';

    /**
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * @var CityRepositoryContract
     */
    private $cities;

    /**
     * @var LocalBusinessService
     */
    private $localBusiness;

    /**
     * @var KlarnaService
     */
    private $klarnaService;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var SchoolService
     */
    private $schoolService;

    /**
     * SchoolController constructor.
     *
     * @param SchoolRepositoryContract $schools
     * @param CityRepositoryContract $cities
     * @param LocalBusinessService $localBusiness
     * @param KlarnaService $klarnaService
     * @param Request $request
     * @param KKJTelegramBotService $botService
     */
    public function __construct(
        SchoolRepositoryContract $schools,
        CityRepositoryContract $cities,
        SchoolService $schoolService,
        LocalBusinessService $localBusiness,
        KlarnaService $klarnaService,
        Request $request,
        KKJTelegramBotService $botService
    )
    {
        parent::__construct($botService);
        $this->schools = $schools;
        $this->cities = $cities;
        $this->localBusiness = $localBusiness;
        $this->klarnaService = $klarnaService;
        $this->request = $request;
        $this->schoolService = $schoolService;
    }

    /**
     * @param null $citySlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($citySlug = null)
    {
        $vehicleId = $this->request->input('vehicle_id', 1);

        if ($citySlug) {
            $city = $this->cities->bySlug($citySlug)->firstOrFail();
            $schools = $this->schools->inCity($city)->with('city')->get();
        } else {
            $city = null;
            $schools = $this->schools->get();
        }

        $title = $this->buildTitle($vehicleId, $city);

        return view('shared::search.index', [
            'title' => $title,
            'city' => $city,
            'courseType' => null,
            'vehicleId' => $vehicleId,
            'resultType' => static::RESULT_TYPE_SCHOOL,
            'schools' => $schools,
        ]);
    }

    /**
     * Show school page
     *
     * @param         $citySlug
     * @param         $schoolSlug
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($citySlug, $schoolSlug, Request $request)
    {
        if ($request->session()->get('klarna_order_id')) {
            $request->session()->forget('klarna_order_id');
            $request->session()->forget('merchant_id');
        }

        $city = $this->cities->bySlug($citySlug)->firstOrFail();
        $school = $this->schools->inCity($city)->bySlug($schoolSlug)->with('upcomingCourses', 'city')->firstOrFail();
        $this->localBusiness->tryParse($school);
        $title = $school->name . ' | ';

        return view('shared::schools.show', [
            'title' => $title,
            'school' => $school,
            'localBusiness' => $this->localBusiness->getLdJsonTag(),
        ]);
    }

    public function rate($schoolId, $courseId, Request $request) {

        $school = $this->schools->query()->findOrFail($schoolId);
        $course = Course::query()->findOrFail($courseId);

        $user = User::query()->where('email', $request->get('email'))->firstOrFail();

        $this->schoolService->rateCourse($school, $course, $user, $request);

        $title = $school->name . ' | ' . trans('vehicle_segments.' . strtolower($course->segment->name));

        if ($request->isMethod('POST')) {
            return view('shared::schools.thanks', [
                'title' => $title,
                'school' => $school,
                'course' => $course,
                'user' => $user,
                'rating' => $request->request->getInt('rating', 1),
                'localBusiness' => $this->localBusiness->getLdJsonTag(),
            ]);
        }

        return view('shared::schools.rate', [
            'title' => $title,
            'school' => $school,
            'course' => $course,
            'user' => $user,
            'rating' => $request->request->getInt('rating', 1),
            'localBusiness' => $this->localBusiness->getLdJsonTag(),
        ]);
    }

    /**
     * Build title
     *
     * @param $vehicleId
     * @param $city
     * @return string
     */
    protected function buildTitle($vehicleId, $city)
    {
        $title = 'Körskolor ';
        if ($vehicleId) {
            switch ($vehicleId) {
                case 1:
                    $title .= 'Bil ';
                    break;
                case 2:
                    $title .= 'MC ';
                    break;
                case 3:
                    $title .= 'Moped ';
                    break;
            }
        }
        $title .= '| Trafikskolor';
        if ($city) {
            $title .= ' i ' . $city->name;
        }

        return $title;
    }
}
