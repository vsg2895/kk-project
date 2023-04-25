<?php namespace Organization\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Jakten\Repositories\Contracts\OrganizationRepositoryContract;
use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\LoyaltyProgramService;
use Shared\Http\Controllers\Controller;
use Jakten\Services\Statistics\StatisticsService;
use Jakten\Facades\Auth;

/**
 * Class StatisticsController
 * @package Organization\Http\Controllers
 */
class StatisticsController extends Controller
{
    /**
     * @var Statistics
     */
    protected $statistics;

    /**
     * @var OrganizationRepositoryContract
     */

    private $organizations;

    /**
     * @var SchoolRepositoryContract
     */

    private $schools;

    /**
     * @var LoyaltyProgramService
     */
    private $loyaltyProgramService;

    /**
     * StatisticsController constructor.
     *
     * @param OrganizationRepositoryContract $organizations
     * @param StatisticsService $statistics
     * @param KKJTelegramBotService $botService
     */
    public function __construct(
        StatisticsService $statistics,
        OrganizationRepositoryContract $organizations,
        SchoolRepositoryContract $schools,
        KKJTelegramBotService $botService,
        LoyaltyProgramService $loyaltyProgramService
    )
    {
        parent::__construct($botService);
        $this->statistics = $statistics;
        $this->organizations = $organizations;
        $this->schools = $schools;
        $this->loyaltyProgramService = $loyaltyProgramService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(int $school_id = null)
    {
        $orgId = Auth::user()->organization_id;
        $organization = $this->organizations->query()->findOrFail($orgId);
        $school = is_null($school_id) ? $organization->schools->first() : $this->schools->query()->findOrFail($school_id);
        $city = $school->city;

        $bookingInfo = $this->statistics->bookWindowInfo($orgId, null, $school->id);
        $avgCourses = $this->statistics->coursesAverage(null, $city);
        $avgCoursesOrg = $this->statistics->coursesAverage($orgId,null, $school->id);
        $totalCourses = $this->statistics->countCoursesByOrg($orgId, $school->id);
        $totalContacts = $this->statistics->countContactsByOrg($orgId, $school->id);

        foreach ($avgCourses as $courses) {
            $courses->name = trans("$courses->name");
        }
        foreach ($avgCoursesOrg as $courses) {
            $courses->name = trans("$courses->name");
        }

        $returnedData = [
            'bookingInfo' => json_encode($bookingInfo),
            'avgCourses' => $avgCourses,
            'avgCoursesOrg' => $avgCoursesOrg,
            'totalCourses' => $totalCourses,
            'totalContacts' => $totalContacts,
            'organization' => $orgId,
            'loyaltyLevels' => collect($this->loyaltyProgramService->loyaltyLevels),
            'schools' => Auth::user()->organization->schools,
        ];

        return request()->wantsJson()
            ? Response::json(['status' => 200, 'view' => view('organization::statistics.custom-filter-part', $returnedData)->render(),'data' => $returnedData])
            : view('organization::statistics.index', $returnedData);

    }
}
