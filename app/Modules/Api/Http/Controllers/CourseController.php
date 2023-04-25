<?php namespace Api\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Jakten\Services\CourseService;
use Jakten\Presenters\SearchedCourses;
use Jakten\Repositories\Contracts\CourseRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Organization\Http\Requests\UpdateCourseCalendarRequest;

/**
 * Class CourseController
 * @package Api\Http\Controllers
 */
class CourseController extends ApiController
{
    /**
     * @var CourseRepositoryContract
     */
    private $courses;

    /**
     * @var CourseService
     */
    private $courseService;

    /**
     * CoursesController constructor.
     *
     * @param CourseRepositoryContract $courses
     * @param CourseService $courseService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(CourseRepositoryContract $courses, CourseService $courseService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->courses = $courses;
        $this->courseService = $courseService;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function find($id)
    {
        $course = $this->courses->query()->with('school.addons', 'school.city', 'school.customAddons', 'city', 'school.logo')->findOrFail($id);
        // use organization logo if school has none
        if (!$course->school->logo && $course->school->organization && $course->school->organization->logo) {
            $course->school->logo()->associate($course->school->organization->logo);
        }

        if ($course->school->logo) {
            $course->school->logo()->associate($course->school->logo->version('small'));
        }

        return $this->success($course);
    }

    /**
     * @param Request $request
     * @param SearchedCourses $searchedCourses
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, SearchedCourses $searchedCourses): \Illuminate\Http\JsonResponse
    {
        $cityId = $request->input('city_id');
        $segments = $request->input('vehicle_segments');
        $vehicleId = $request->input('vehicle_id');
        $from = $request->input('from');
        $to = $request->input('to');
        $schoolIds = $request->input('schools');
        $bounds = $request->input('bounds');
        $page = $request->input('page');
        $calendar = (int)$request->input('calendar');

        $cacheKey = 'SEARCH_COURSES_' . $cityId . $segments . $vehicleId . $from . $to . $schoolIds . $bounds . $page . $calendar;

//        if (Cache::has($cacheKey)) {
//            $data = Cache::get($cacheKey);
//        } else {

            $searchData = $this->courseService->search($request);

            if ($calendar) {
                $courses = $searchedCourses->format(collect($searchData ?: []));
                $data = ['courses' => $courses];
            } else {
                $courses = $searchedCourses->format(collect($searchData ? $searchData->items() : []));
                $data = ['courses' => $courses, 'total' => $searchData->total(), 'last_page' => $searchData->lastPage()];
            }

//            Cache::put($cacheKey, $data, Carbon::now()->addHours(1));
//        }

        return $this->success($data);
    }

    /**
     * @param Request $request
     * @param SearchedCourses $searchedCourses
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, UpdateCourseCalendarRequest $request)
    {
        $course = $this->courses->query()->withTrashed()->findOrFail($id);
        $course = $this->courseService->updateCourse($course, $request);

        return $this->success(['course' => $course]);
    }
}
