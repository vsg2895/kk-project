<?php namespace Admin\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\MessageBag;
use Admin\Http\Requests\StoreCourseRequest;
use Admin\Http\Requests\UpdateCourseRequest;
use Illuminate\Validation\UnauthorizedException;
use Jakten\Console\Commands\ReActivateCourses;
use Jakten\Jobs\ActivateCourses;
use Jakten\Jobs\PromoteCourses;
use Jakten\Jobs\ReminderCourses;
use Jakten\Models\Course;
use Jakten\Models\School;
use Jakten\Presenters\SimplifiedCities;
use Jakten\Repositories\Contracts\CityRepositoryContract;
use Jakten\Repositories\Contracts\CourseRepositoryContract;
use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use Jakten\Repositories\Contracts\VehicleSegmentRepositoryContract;
use Jakten\Repositories\OrderItemRepository;
use Jakten\Repositories\SchoolRepository;
use Jakten\Services\CourseService;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\OrderService;
use Jakten\Services\SchoolService;
use Shared\Http\Controllers\Controller;
use Shared\Http\Requests\BaseFormRequest;
use Barryvdh\DomPDF\Facade as PDF;

/**
 * Class CourseController
 *
 * @package Admin\Http\Controllers
 */
class CourseController extends Controller
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
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * @var CityRepositoryContract
     */
    private $cities;

    /**
     * @var VehicleSegmentRepositoryContract
     */
    private $vehicleSegments;

    /**
     * CoursesController constructor.
     *
     * @param CourseRepositoryContract $courses
     * @param CourseService $courseService
     * @param SchoolRepositoryContract $schools
     * @param CityRepositoryContract $cities
     * @param VehicleSegmentRepositoryContract $vehicleSegments
     * @param KKJTelegramBotService $botService
     */
    public function __construct(
        CourseRepositoryContract $courses,
        CourseService $courseService,
        SchoolRepositoryContract $schools,
        CityRepositoryContract $cities,
        VehicleSegmentRepositoryContract $vehicleSegments,
        KKJTelegramBotService $botService
    )
    {
        parent::__construct($botService);
        $this->courses = $courses;
        $this->courseService = $courseService;
        $this->schools = $schools;
        $this->cities = $cities;
        $this->vehicleSegments = $vehicleSegments;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index()
    {
        $courses = $this->courses->inFuture()->query()->orderBy('start_time')->paginate();
        $this->courses->reset();
        $oldCourses = $this->courses->old()->query()
            ->orderBy('start_time', 'desc')->paginate();

        return view('admin::courses.index', [
            'courses' => $courses,
            'oldCourses' => $oldCourses,
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $course = $this->courses->query()->withTrashed()->findOrFail($id);

        return view('admin::courses.show', [
            'course' => $course,
        ]);
    }

    /**
     * @param                     $id
     * @param UpdateCourseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, UpdateCourseRequest $request)
    {
        $course = $this->courses->query()->withTrashed()->findOrFail($id);
        $course = $this->courseService->updateCourse($course, $request);

        $request->session()->flash('message', 'Kurs uppdaterad!');

        return redirect()->route('admin::courses.show', ['id' => $course->id]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $course = null;
        $courseId = $request->get('initialCourse', false);

        if ($courseId) {
            $course = $this->courses->query()->with('bookings')->find($courseId);

            if ($course instanceof Course) {
                $course = array_except($course->toArray(), ['id', 'digital_shared']);
            }
        }

        return view('admin::courses.create', [
            'initialSchool' => $request->get('school'),
            'initialCourse' => $course,
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function downloadParticipants($id)
    {
        $course = $this->courses->query()->with('bookings')->findOrFail($id);
        $this->authorize('view', $course);

        $pdf = PDF::loadView('organization::courses.download-participants', ['bookings' => $course->bookings->where('cancelled', false)->groupBy('order_id')]);

        return $pdf->stream($course->id . '_' . $course->school->name .'_participants.pdf');
    }

    /**
     * @param StoreCourseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCourseRequest $request)
    {
        /** @var Course $course */
        $course = $this->courseService->storeCourse($request);

        $this->dispatch(new ReminderCourses($course));
        $this->dispatch(new PromoteCourses($course));

        $request->session()->flash('message', 'Kurs skapad!');

        return redirect()->route('admin::courses.show', ['id' => $course->id]);
    }

    public function orderStore(Request $request) {

        $this->courseService->storeCourseOrder($request);

        /* php artisan cache:clear */
        \Illuminate\Support\Facades\Artisan::call('cache:clear');

        return redirect()->route('admin::courses.order');
    }

    public function order(SchoolRepositoryContract $schoolRepository, VehicleSegmentRepositoryContract $vehicleSegmentContract, SimplifiedCities $presenter, Request $request) {
        $date = $request->get('date') ?: Carbon::now()->format('Y-m-d');

        $schools = $schoolRepository->hasCoursesOnTheDate(
            $date,
            $request->get('city'),
            $request->get('type')
        )->get();

        $vehicles = $vehicleSegmentContract->forDate($date, $request->get('city'))->get();
        $cities = [];

        foreach ($schools as $val) {
            if (!in_array($val->city, $cities)) {
                $cities[] = $val->city;
            }
        }

        foreach ($vehicles as $key => $veh) {
            if (in_array($veh->id, [\Jakten\Models\VehicleSegment::PUBLIC_DIGITAL_COURSE_ID, \Jakten\Models\VehicleSegment::ONLINE_LICENSE_THEORY])) {
                unset($vehicles[$key]);
            }
        }

        return view('admin::courses.order', [
            'vehicles' => $vehicles,
            'cities' => $cities,
            'schools' => $schools,
            'date' => $date,
            'selectedCity' => $request->get('city', 'false'),
            'selectedType' => $request->get('type', 'false')
        ]);
    }

    /**
     * @param         $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete($id, Request $request)
    {
        $course = $this->courses->query()->with('bookings')->findOrFail($id);
        $this->authorize('delete', $course);
        try {
            $this->courseService->delete($course);
            $request->session()->flash('message', 'Kursen har tagits bort');
        } catch (UnauthorizedException $exception) {
            $message = new MessageBag();
            $message->add('has_bookings', 'Du kan inte ta bort en kurs med bokningar.');

            $request->session()->flash('errors', $message);

            return redirect()->back();
        }

        return redirect()->route('admin::courses.index')->with('message', 'Kurs borttagen!');
    }
}
