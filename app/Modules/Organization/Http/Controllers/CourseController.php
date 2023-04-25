<?php namespace Organization\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\UnauthorizedException;
use Jakten\Facades\Auth;
use Jakten\Jobs\ActivateCourses;
use Jakten\Jobs\PromoteCourses;
use Jakten\Jobs\ReminderCourses;
use Jakten\Models\Course;
use Jakten\Repositories\Contracts\{CityRepositoryContract, CourseRepositoryContract, VehicleSegmentRepositoryContract};
use Jakten\Models\VehicleSegment;
use Jakten\Repositories\OrderItemRepository;
use Jakten\Services\CourseService;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\LoyaltyProgramService;
use Jakten\Services\OrderService;
use Organization\Http\Requests\{StoreCourseRequest, UpdateCourseRequest};
use Shared\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;

/**
 * Class CourseController
 *
 * @package Organization\Http\Controllers
 */
class CourseController extends Controller
{
    /**
     * @var CourseRepositoryContract
     */
    private $courses;

    /**
     * @var CityRepositoryContract
     */
    private $cities;

    /**
     * @var CourseService
     */
    private $courseService;

    /**
     * @var VehicleSegmentRepositoryContract
     */
    private $segments;

    /**
     * @var LoyaltyProgramService
     */
    private $loyaltyProgramService;

    /**
     * BookingController constructor.
     *
     * @param CourseRepositoryContract $courses
     * @param CityRepositoryContract $cities
     * @param CourseService $courseService
     * @param VehicleSegmentRepositoryContract $segments
     * @param KKJTelegramBotService $botService
     */
    public function __construct(
        CourseRepositoryContract $courses,
        CityRepositoryContract $cities,
        CourseService $courseService,
        VehicleSegmentRepositoryContract $segments,
        KKJTelegramBotService $botService,
        LoyaltyProgramService $loyaltyProgramService
    )
    {
        parent::__construct($botService);
        $this->courses = $courses;
        $this->cities = $cities;
        $this->courseService = $courseService;
        $this->segments = $segments;
        $this->loyaltyProgramService = $loyaltyProgramService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $calendarView = $request->get('calendar', false);

        $user = Auth::user();
        $shared = \Jakten\Models\VehicleSegment::SHARED_COURSES;

        $coursesQuery = $this->courses->byOrganization($user->organization)
            ->inFuture()
            ->query()
            ->with('school')->with('bookings')
            ->whereNotIn('vehicle_segment_id', $shared);


        $this->courses->reset();
        $oldCoursesQuery = $this->courses->byOrganization($user->organization)
            ->old()->query()
            ->whereNotIn('vehicle_segment_id', $shared)
            ->with('school')->with('bookings');

        $coursesSegments = $coursesQuery->pluck('vehicle_segment_id')->unique()->toArray();
        $oldCoursesSegments = $coursesQuery->pluck('vehicle_segment_id')->unique()->toArray();
        $allCoursesVehicleSegments = array_unique(array_merge($coursesSegments, $oldCoursesSegments));

        if ($request->has('vehicle_segment_id')) {
            $coursesQuery = $coursesQuery->where('vehicle_segment_id', $request->vehicle_segment_id);
            $oldCoursesQuery = $oldCoursesQuery->where('vehicle_segment_id', $request->vehicle_segment_id);
        }
        if ($request->has('school_id')) {
            $coursesQuery = $coursesQuery->where('school_id', $request->school_id);
            $oldCoursesQuery = $oldCoursesQuery->where('school_id', $request->school_id);
        }

        $courses = $calendarView ? $coursesQuery->orderBy('start_time')->get() : $coursesQuery->orderBy('start_time')->paginate();

        $oldCourses = $calendarView
            ? $oldCoursesQuery->where('start_time', '>=', Carbon::now()->subDay(90))->orderByDesc('start_time')->get()
            : $oldCoursesQuery->orderBy('start_time', 'desc')->paginate();

        $coursesVehicleSegments = VehicleSegment::whereIn('id',$allCoursesVehicleSegments)->get();

        return view($calendarView ? 'organization::courses.calendar' : 'organization::courses.index', [
            'courses' => $calendarView ? $oldCourses->merge($courses) : $courses,
            'oldCourses' => $oldCourses,
            'schools' => $user->organization->schools,
            'schoolsCount' => $user->organization->schools->count(),
            'loyaltyLevels' => collect($this->loyaltyProgramService->loyaltyLevels),
            'coursesVehicleSegments' => $coursesVehicleSegments,
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        $course = $this->courses->query()->with('bookings')->findOrFail($id);
        $this->authorize('view', $course);

        return view('organization::courses.show', [
            'initialSchool' => $course->school,
            'course' => $course,
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
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request)
    {
        $this->authorize('create', Course::class);

        $courseId = $request->get('initialCourse');
        $course = $this->courses->query()->with('bookings')->find($courseId);

        $vehicleSegmentId = $request->get('courseType');
        $schoolId = $request->get('school');
        $startTime = $request->get('start');

        if ($vehicleSegmentId && $schoolId) {
            $course = Course::query()->with('bookings')
                ->where('school_id', (string)$schoolId)
                ->where('vehicle_segment_id', (string)$vehicleSegmentId)->orderByDesc('start_time')->first();
        }

        if ($course instanceof Course) {
            $course = array_except($course->toArray(), ['id', 'digital_shared']);
        }

        if ($startTime) {
            $course['start_time'] = (new Carbon($startTime))->toDateTimeString();
        }

        return view('organization::courses.create', [
            'initialSchool' => $request->get('school'),
            'initialCourse' => $course,
        ]);
    }

    /**
     * @param StoreCourseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreCourseRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('create', Course::class);

        /** @var Course $course */
        $course = $this->courseService->storeCourse($request);

        $this->dispatch(new ReminderCourses($course));
        $this->dispatch(new PromoteCourses($course));

        $request->session()->flash('message', 'Kurs skapad!');
        return redirect()->route('organization::courses.show', $course->id);
    }


    /**
     * @param $id
     * @param UpdateCourseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update($id, UpdateCourseRequest $request)
    {
        $course = $this->courses->query()->with('bookings')->findOrFail($id);
        $this->authorize('view', $course);

        $this->courseService->updateCourse($course, $request);

        $request->session()->flash('message', 'Kurs uppdaterad!');

        return redirect()->route('organization::courses.show', ['id' => $course->id]);
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

        return redirect()->route('organization::courses.index')->with('message', 'Kurs borttagen!');
    }
}
