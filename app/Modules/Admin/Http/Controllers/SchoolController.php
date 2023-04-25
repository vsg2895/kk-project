<?php namespace Admin\Http\Controllers;

use Admin\Http\Requests\{StoreSchoolCommentRequest,
    StoreSchoolRequest,
    UpdateSchoolDetailRequest,
    UpdateSchoolPricesRequest};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Jakten\Exports\SchoolsExport;
use Jakten\Models\{Addon, CustomAddon, VehicleSegment};
use Jakten\Modules\Shared\Http\Requests\FilterByVehicleSegmentRequest;
use Jakten\Repositories\Contracts\{CityRepositoryContract,
    OrganizationRepositoryContract,
    SchoolClaimRepositoryContract,
    SchoolRepositoryContract};
use Jakten\Services\{Annotation\AnnotationService,
    KKJTelegramBotService,
    LoyaltyProgramService,
    RuleAPIService,
    SchoolService};
use Maatwebsite\Excel\Excel;
use Shared\Http\Controllers\Controller;

/**
 * Class SchoolController
 * @package Admin\Http\Controllers
 */
class SchoolController extends Controller
{
    /**
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * @var SchoolService
     */
    private $schoolService;

    /**
     * @var CityRepositoryContract
     */
    private $cities;

    /**
     * @var OrganizationRepositoryContract
     */
    private $organizations;

    /**
     * @var SchoolClaimRepositoryContract
     */
    private $claims;
    /**
     * @var LoyaltyProgramService
     */
    private $loyaltyProgramService;
    /**
     * @var RuleAPIService
     */
    private $ruleAPIService;

    /**
     * SchoolsController constructor.
     *
     * @param SchoolRepositoryContract $schools
     * @param SchoolService $schoolService
     * @param CityRepositoryContract $cities
     * @param OrganizationRepositoryContract $organizations
     * @param SchoolClaimRepositoryContract $claims
     * @param KKJTelegramBotService $botService
     */
    public function __construct(
        SchoolRepositoryContract $schools,
        SchoolService $schoolService,
        CityRepositoryContract $cities,
        OrganizationRepositoryContract $organizations,
        SchoolClaimRepositoryContract $claims,
        KKJTelegramBotService $botService,
        LoyaltyProgramService $loyaltyProgramService,
        RuleAPIService $ruleAPIService
    )
    {
        parent::__construct($botService);
        $this->schools = $schools;
        $this->schoolService = $schoolService;
        $this->cities = $cities;
        $this->organizations = $organizations;
        $this->claims = $claims;
        $this->loyaltyProgramService = $loyaltyProgramService;
        $this->ruleAPIService = $ruleAPIService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $schools = $this->schools->search($request)
            ->with('claims', 'courses', 'upcomingCourses')
            ->orderBy('name')
            ->paginate()
            ->appends(Input::except('page'));

        return view('admin::schools.index', [
            'schools' => $schools
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(FilterByVehicleSegmentRequest $request,int $id)
    {
        $school = $this->schools->query()->with('prices.segment', 'logo', 'availableVehicles', 'usps')->withTrashed()->findOrFail($id);
        $addons = Addon::all();
        $cities = $this->cities->getForSelect();
        $claims = $this->claims->ofSchool($school)->get();
        $organizations = $this->organizations->query()->whereNotIn('id', $claims->pluck('organization_id')->all())->get();
        $customAddons = CustomAddon::where('school_id', $school->id)->get();

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

            $sortedAddon = array_merge($ordered, $addons->toArray());
        }

        $defaultFees = DB::table('vehicle_fee')->get();
        $pricesDefaults = [];

        foreach ($defaultFees as $val) {
            $pricesDefaults[$val->id] = $val->fee;
        }

        $coursesVehicleSegments = VehicleSegment::whereIn('id', $school->courses->pluck('vehicle_segment_id')->unique())->get();
        $upcomingCourses = $this->schools->coursesByTypeVehicleFilter($school, $request->vehicle_segment_id)['upcomingCourses'];
        $completedCourses = $this->schools->coursesByTypeVehicleFilter($school, $request->vehicle_segment_id)['completedCourses'];

        return view('admin::schools.show', [
            'school' => $school,
            'cities' => $cities,
            'organizations' => $organizations,
            'claims' => $claims,
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
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $cities = $this->cities->getForSelect();
        $organizations = $this->organizations->query()->get();
        $initialOrganization = $request->input('organization');

        return view('admin::schools.create', [
            'cities' => $cities,
            'organizations' => $organizations,
            'initialOrganization' => $initialOrganization,
        ]);
    }

    /**
     * @param StoreSchoolRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreSchoolRequest $request)
    {
        $school = $this->schoolService->storeSchool($request);
        $request->session()->flash('message', 'Trafikskola skapad!');

        return redirect()->route('admin::schools.show', ['id' => $school->id]);
    }

    /**
     * @param $id
     * @param UpdateSchoolDetailRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDetails($id, UpdateSchoolDetailRequest $request)
    {
        $school = $this->schools->query()->withTrashed()->findOrFail($id);
        $school = $this->schoolService->updateSchool($school, $request);

        $request->session()->flash('message', 'Trafikskola uppdaterad!');

        return redirect()->route('admin::schools.show', ['id' => $school->id]);
    }

    /**
     * @param $id
     * @param UpdateSchoolPricesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePrices($id, UpdateSchoolPricesRequest $request)
    {
        $school = $this->schools->query()->withTrashed()->findOrFail($id);
        $school = $this->schoolService->updateSchool($school, $request);

        $request->session()->flash('message', 'Trafikskola uppdaterad!');

        return redirect()->route('admin::schools.show', ['id' => $school->id]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFees($id, Request $request)
    {
        $school = $this->schools->query()->withTrashed()->findOrFail($id);
        $this->schoolService->updateFees($school, $request);

        $request->session()->flash('message', 'Trafikskola uppdaterad!');

        return redirect()->route('admin::schools.show', ['id' => $school->id]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete($id)
    {
        $school = $this->schools->query()->findOrFail($id);
        $this->schoolService->delete($school);

        return redirect()->route('admin::schools.index')->with('message', 'Trafikskola borttagen!');
    }

    /**
     * @param StoreSchoolCommentRequest $request
     * @param $id
     * @param AnnotationService $annotationService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createComment(StoreSchoolCommentRequest $request, $id, AnnotationService $annotationService)
    {
        $school = $this->schools->query()->withTrashed()->findOrFail($id);

        $message = $request->get('message');
        $type = $request->get('type');

        $annotation = $annotationService->create($message, $type);
        $school->comments()->save($annotation);
        return redirect()->route('admin::schools.show', ['id' => $school->id, '#comments']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addCustomAddon(Request $request)
    {
        $schoolId = $request->input('school-id');
        $name = $request->input('name');
        if ($name && $schoolId) {
            $addon = new CustomAddon;
            $addon->name = $name;
            $addon->school_id = $schoolId;
            $addon->save();
        }
        $request->session()->flash('message', 'Ny tilläggsprodukt "' . $name . '" skapad!');
        return redirect()->route('admin::schools.show', ['id' => $schoolId, '#prices']);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function removeCustomAddon($id, Request $request)
    {
        $addon = CustomAddon::with('school')->find($id);
        $name = $addon->name;
        $schoolId = $addon->school->id;
        if ($addon && $schoolId) {
            $addon->active = false;
            $addon->save();
            $addon->delete();
            $request->session()->flash('message', 'Tilläggsprodukt "' . $name . '" bortagen!');
        }
        return redirect()->route('admin::schools.show', ['id' => $schoolId, '#prices']);
    }

    /**
     * @param Excel $excel
     * @param SchoolsExport $schoolsExport
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportSchoolsList(Excel $excel, SchoolsExport $schoolsExport)
    {
        return $excel->download($schoolsExport, 'schools_list.xlsx');
    }
}
