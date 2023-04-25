<?php namespace Shared\Http\Controllers;

use Illuminate\Http\Request;
use Jakten\Services\KKJTelegramBotService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Jakten\Repositories\Contracts\{CityRepositoryContract, SchoolRepositoryContract, VehicleSegmentRepositoryContract};

/**
 * Class SearchController
 * @package Shared\Http\Controllers
 */
class SearchController extends Controller
{
    /**
     * @constant RESULT_TYPE_SCHOOL
     */
    const RESULT_TYPE_SCHOOL = 'SCHOOL';

    /**
     * @constant RESULT_TYPE_COURSE
     */
    const RESULT_TYPE_COURSE = 'COURSE';

    /**
     * @var CityRepositoryContract
     */
    private $cities;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var VehicleSegmentRepositoryContract
     */
    private $vehicleSegments;
    /**
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * IndexController constructor.
     *
     * @param CityRepositoryContract $cities
     * @param Request $request
     * @param VehicleSegmentRepositoryContract $vehicleSegments
     * @param SchoolRepositoryContract $schools
     * @param KKJTelegramBotService $botService
     */
    public function __construct(CityRepositoryContract $cities, Request $request, VehicleSegmentRepositoryContract $vehicleSegments, SchoolRepositoryContract $schools, KKJTelegramBotService $botService)

    {
        parent::__construct($botService);
        $this->cities = $cities;
        $this->request = $request;
        $this->vehicleSegments = $vehicleSegments;
        $this->schools = $schools;
    }

    /**
     * @param null $type
     * @param null $courseType
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($type = null, $courseType = null)
    {
        $resultType = $this->parseCourseType($type);
        $vehicleId = $this->request->input('vehicle_id', 1);
        $courseTypeId = $this->getCourseTypeId($courseType, $vehicleId);
        $schools = $this->schools->with('city')->get();

        return view('shared::search.index', [
            'city' => null,
            'courseType' => $courseTypeId,
            'vehicleId' => $this->request->input('vehicle_id'),
            'resultType' => $resultType,
            'schools' => $schools
        ]);
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function indexSchools($slug)
    {
        $city = $this->getCity($slug);
        $vehicleId = $this->request->input('vehicle_id', 1);

        if ($slug && !$city) {
            return redirect()->route('shared::search.index');
        } else {
            $schools = $this->schools->inCity($city)->with('city')->get();

            return view('shared::search.index', [
                'city' => $city,
                'courseType' => null,
                'vehicleId' => $vehicleId,
                'resultType' => static::RESULT_TYPE_SCHOOL,
                'schools' => $schools
            ]);
        }
    }

    /**
     * @param $slug
     * @param null $courseType
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function indexCourses($slug, $courseType = null)
    {
        $courseTypeId = null;
        $vehicleId = $this->request->input('vehicle_id', 1);

        if ($courseType) {
            $courseTypeId = $this->getCourseTypeId($courseType, $vehicleId);
        }

        $city = $this->getCity($slug);

        if ($slug && !$city) {
            return redirect()->route('shared::search.index');
        } else {


            return view('shared::search.index', [
                'city' => $city,
                'vehicleId' => $vehicleId,
                'courseType' => $courseTypeId,
                'resultType' => static::RESULT_TYPE_COURSE,
            ]);
        }
    }

    /**
     * Handle possible redirect to route without
     * location or fetch the correct location
     *
     * @param $slug
     *
     * @return mixed|null
     */
    protected function getCity($slug)
    {
        if ($slug) {
            return $this->cities->bySlug($slug)->first();
        } else {
            return null;
        }
    }

    /**
     * @param $string
     * @return string
     */
    private function parseCourseType($string)
    {
        if ($string == 'kurser') {
            return static::RESULT_TYPE_COURSE;
        } elseif ($string == 'trafikskolor') {
            return static::RESULT_TYPE_SCHOOL;
        }

        return static::RESULT_TYPE_SCHOOL;
    }

    /**
     * @param $courseType
     * @param $vehicleId
     * @return null
     */
    private function getCourseTypeId($courseType, $vehicleId)
    {
        $courseTypeId = null;

        if ($courseType) {
            $found = false;
            $segments = $this->vehicleSegments->bookable()->get();
            foreach ($segments as $segment) {
                if ($courseType == strtolower(trans('vehicle_segments.' . strtolower($segment->name))) && $segment->vehicle_id == $vehicleId) {
                    $courseTypeId = $segment->id;
                    $found = true;
                }
            }

            if (!$found) {
                throw new NotFoundHttpException();
            }
        }

        return $courseTypeId;
    }
}
