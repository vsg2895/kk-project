<?php namespace Admin\Http\Controllers;

use Illuminate\Http\Request;
use Jakten\Exports\MonthlyReportExport;
use Jakten\Exports\UserExport;
use Jakten\Facades\Auth;
use Jakten\Repositories\Contracts\OrganizationRepositoryContract;
use Jakten\Repositories\Contracts\VehicleSegmentRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Maatwebsite\Excel\Excel;
use Nette\Http\Session;
use Shared\Http\Controllers\Controller;
use Jakten\Services\Statistics\StatisticsService;

/**
 * Class StatisticsController
 * @package Admin\Http\Controllers
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
     * @var VehicleSegmentRepositoryContract
     */
    private $vehicleSegments;

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
        KKJTelegramBotService $botService,
        VehicleSegmentRepositoryContract $vehicleSegments
    )
    {
        parent::__construct($botService);
        $this->statistics = $statistics;
        $this->organizations = $organizations;
        $this->vehicleSegments = $vehicleSegments;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $totalCourses = $this->statistics->coursesCount();
        $totalSchools = $this->statistics->schools();

        $users = $this->statistics->userTypes();
        $topOrganizations = $this->statistics->topFiveForContacts();

        $segments = $this->vehicleSegments->get();

        return view('admin::statistics.index', [
            'totalCourses' => number_format(floatval($totalCourses), 0, '', ' '),
            'totalSchools' => number_format(floatval(reset($totalSchools)), 0, '', ' '),
            'users' => json_encode($users),
            'topOrganizations' => $topOrganizations,
            'segments' => $segments,
        ]);
    }

    /**
     * @param $orgId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginAsOrganization($orgId, Request $request)
    {
        $organization = $this->organizations->query()->findOrFail($orgId);

        foreach ($organization->users as $user) {
            if ($user->confirmed && !$user->deleted_at && $user->isOrganizationUser()) {
                $request->session()->put('adminId', Auth::user()->id);
                Auth::guard()->logout();
                Auth::loginUsingId($user->id, true);
            }
        }

        $request->session()->flash('errors', 'There is no active user for this organization');

        return redirect()->back();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function organization($orgId)
    {
        $organization = $this->organizations->query()->with('logo')->withTrashed()->findOrFail($orgId);

        $bookingInfo = $this->statistics->bookWindowInfo($organization->id);
//        $avgCourses = $this->statistics->coursesAverage(null,$organization->schools->first()->city->id);
        $avgCourses = $this->statistics->coursesAverage(null,$organization->schools->first()->city);
        $avgCoursesOrg = $this->statistics->coursesAverage($organization->id);
        $totalCourses = $this->statistics->countCoursesByOrg($organization->id);
        $totalContacts = $this->statistics->countContactsByOrg($organization->id);

        return view('admin::statistics.organization', [
            'bookingInfo' => json_encode($bookingInfo),
            'avgCourses' => $avgCourses,
            'avgCoursesOrg' => $avgCoursesOrg,
            'totalCourses' => $totalCourses,
            'totalContacts' => $totalContacts,
            'organization' => $orgId
        ]);
    }

    public function exportUserList(Excel $excel, UserExport $userExport)
    {
        return $excel->download($userExport, 'user_list.xlsx');
    }

    public function exportMonthlyReport(Excel $excel, MonthlyReportExport $userExport)
    {
        return $excel->download($userExport, request()->is_daily ? 'Report By Days.xlsx' : 'Monthly Report.xlsx');
    }

}
