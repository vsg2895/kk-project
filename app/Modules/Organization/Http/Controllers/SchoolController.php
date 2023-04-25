<?php namespace Organization\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jakten\Facades\Auth;
use Jakten\Models\{Addon, CustomAddon, VehicleSegment};
use Jakten\Repositories\Contracts\{CityRepositoryContract, SchoolRepositoryContract};
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\LoyaltyProgramService;
use Jakten\Services\SchoolService;
use Organization\Http\Requests\{StoreSchoolRequest, UpdateSchoolDetailsRequest, UpdateSchoolPricesRequest};
use Shared\Http\Controllers\Controller;

/**
 * Class SchoolController
 * @package Organization\Http\Controllers
 */
class SchoolController extends Controller
{
    /**
     * @var SchoolService
     */
    private $schoolService;

    /**
     * @var SchoolRepositoryContract
     */
    private $schools;
    /**
     * @var LoyaltyProgramService
     */
    private $loyaltyProgramService;

    /**
     * SchoolController constructor.
     *
     * @param SchoolService $schoolService
     * @param SchoolRepositoryContract $schools
     * @param KKJTelegramBotService $botService
     */
    public function __construct(
        SchoolService $schoolService,
        SchoolRepositoryContract $schools,
        KKJTelegramBotService $botService,
        LoyaltyProgramService $loyaltyProgramService
    )
    {
        parent::__construct($botService);
        $this->schoolService = $schoolService;
        $this->schools = $schools;
        $this->loyaltyProgramService = $loyaltyProgramService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $schools = $this->schools->belongsTo(Auth::user()->organization)->query()->paginate();

        return view('organization::schools.index', [
            'schools' => $schools,
        ]);
    }

    /**
     * @param $id
     * @param CityRepositoryContract $cityRepo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id, CityRepositoryContract $cityRepo, Request $request)
    {
        $school = $this->schools->query()->with('prices.segment.vehicle', 'addons', 'logo', 'availableVehicles')->findOrFail($id);
        $addons = Addon::all();
        $customAddons = CustomAddon::where('school_id', $school->id)->get();
        $this->authorize('view', $school);

        $sortedAddon = $addons;
        if($school->addons) {
            $ordered = [];
            foreach ($school->addons->sortBy('pivot.sort_order')->values()->toArray() as $val) {
                foreach ($addons as $key => $addonsVal) {
                    if ($addonsVal['id'] === $val['id']) {
                        $ordered[] = $addonsVal;
                        unset($sortedAddon[$key]);
                    }
                }
            }
            $sortedAddon = $ordered + $addons->toArray();
        }

        $defaultFees = DB::table('vehicle_fee')->get();
        $pricesDefaults = [];

        foreach ($defaultFees as $val) {
            $pricesDefaults[$val->id] = $val->fee;
        }

        $coursesVehicleSegments = VehicleSegment::whereIn('id',$school->courses->pluck('vehicle_segment_id')->unique())->get();

        $upcomingCourses = $this->schools->coursesByTypeVehicleFilter($school, $request->vehicle_segment_id)['upcomingCourses'];
        $completedCourses = $this->schools->coursesByTypeVehicleFilter($school, $request->vehicle_segment_id)['completedCourses'];

        return view('organization::schools.show', [
            'school' => $school,
            'addons' => $sortedAddon,
            'pricesDefaults' => $pricesDefaults,
            'customAddons' => $customAddons,
            'prices' => $school->prices->sortBy('segment.vehicle_id'),
            'schoolLoyaltyData' => collect($this->loyaltyProgramService->getLoyaltyLevelBasedOnSchool($school)),
            'loyaltyLevels' => collect($this->loyaltyProgramService->loyaltyLevels),
            'topPartnerFee' => $school->top_partner ? 2.5 : 0,
            'coursesVehicleSegments' => $coursesVehicleSegments,
            'upcomingCourses' => $upcomingCourses,
            'completedCourses' => $completedCourses,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('organization::schools.create');
    }

    /**
     * @param StoreSchoolRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreSchoolRequest $request)
    {
        $school = $this->schoolService->storeSchool($request, Auth::user()->organization);

        $request->session()->flash('message', 'Trafikskola skapad!');

        return redirect()->route('organization::schools.show', ['id' => $school->id]);
    }

    /**
     * @param $id
     * @param UpdateSchoolDetailsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateDetails($id, UpdateSchoolDetailsRequest $request)
    {
        $school = $this->schools->query()->findOrFail($id);
        $this->authorize('update', $school);
        $this->schoolService->updateSchool($school, $request, true);

        $request->session()->flash('message', 'Trafikskola uppdaterad!');

        return redirect()->route('organization::schools.show', ['id' => $school->id]);
    }

    /**
     * @param $id
     * @param UpdateSchoolPricesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updatePrices($id, UpdateSchoolPricesRequest $request)
    {
        $school = $this->schools->query()->findOrFail($id);
        $this->authorize('update', $school);
        $this->schoolService->updateSchool($school, $request);

        $request->session()->flash('message', 'Trafikskola uppdaterad!');

        return redirect()->route('organization::schools.show', ['id' => $school->id]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addCustomAddon(Request $request)
    {
        $schoolId = $request->input('school-id');
        $schoolOrgId = $this->schools->findOrFail($schoolId)->first()->organization_id;
        $userOrgId = Auth::user()->organization_id;
        $name = $request->input('name');
        if ($name && $schoolId && $schoolOrgId === $userOrgId) {
            $addon = new CustomAddon;
            $addon->name = $name;
            $addon->school_id = $schoolId;
            $addon->save();
        }
        $request->session()->flash('message', 'Ny tilläggsprodukt "' . $name . '" skapad!');
        return redirect()->route('organization::schools.show', ['id' => $schoolId, '#prices']);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeCustomAddon($id, Request $request)
    {
        $addon = CustomAddon::with('school')->find($id);
        $name = $addon->name;
        $schoolId = $addon->school->id;
        $schoolOrgId = $this->schools->findOrFail($schoolId)->first()->organization_id;
        $userOrgId = Auth::user()->organization_id;
        if ($addon && $schoolId && $schoolOrgId === $userOrgId) {
            $addon->active = false;
            $addon->save();
            $addon->delete();
            $request->session()->flash('message', 'Tilläggsprodukt "' . $name . '" bortagen!');
        }
        return redirect()->route('organization::schools.show', ['id' => $schoolId, '#prices']);
    }
}
