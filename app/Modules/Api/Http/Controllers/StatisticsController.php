<?php namespace Api\Http\Controllers;

use Carbon\Carbon;
use Jakten\Facades\Auth;
use Jakten\Models\{City, School, Organization};
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\Statistics\{OrderStatisticsService, StatisticsService};

/**
 * Class StatisticsController
 * @package Api\Http\Controllers
 */
class StatisticsController extends ApiController
{
    /**
     * @var City
     */
    protected $cites;

    /**
     * @var School
     */
    protected $schools;

    /**
     * @var Statistics
     */
    protected $statistics;

    /**
     * @var Organization
     */
    protected $organizations;

    /**
     * @var OrderStatisticsService
     */
    private $orderStatisticsService;

    /**
     * StatisticsController constructor.
     *
     * @param City $cites
     * @param School $schools
     * @param StatisticsService $statistics
     * @param Organization $organizations
     * @param OrderStatisticsService $orderStatisticsService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(City $cites, School $schools, StatisticsService $statistics, Organization $organizations, OrderStatisticsService $orderStatisticsService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->cites = $cites;
        $this->schools = $schools;
        $this->statistics = $statistics;
        $this->organizations = $organizations;
        $this->orderStatisticsService = $orderStatisticsService;
    }

    /**
     * ATTN: for Summary vue component
     *
     * @param $startDate
     * @param $endDate
     * @param $granularity
     * @param $type
     * @param int $cityId
     * @param int $orgId
     * @param int $schoolId
     * @return \Illuminate\Http\JsonResponse
     */
    public function orders($startDate, $endDate, $granularity, $type, int $cityId, int $orgId, int $schoolId)
    {
        $groupBy = Auth::user()->isOrganizationUser() ? 'school' : $type;
        $orgId = Auth::user()->isOrganizationUser() ? Auth::user()->organization_id : $orgId;
        $startDate = Carbon::createFromFormat('Y-m-d', $startDate);
        $endDate = Carbon::createFromFormat('Y-m-d', $endDate);
        $data = collect(['cityId' => $cityId, 'organizationId' => $orgId, 'schoolId' => $schoolId, 'granularity' => $granularity]);
        $statistics = $this->orderStatisticsService->getStatistics($groupBy, $startDate, $endDate, $data);
        $statisticsLastYear = $this->orderStatisticsService->getStatistics($groupBy, $startDate->subYear(), $endDate->subYear(), $data);
        $title = 'Sverige';

        if ($schoolId > 0) {
            $schoolName = School::query()->findOrFail($schoolId)->name;
            $title = $schoolName;
        } else {
            if ($orgId > 0) {
                $orgName = Organization::query()->findOrFail($orgId)->name;
                $title = $orgName;

                if ($cityId > 0) {
                    $cityName = City::query()->findOrFail($cityId)->name;
                    $title .= ' i ' . $cityName;
                }
            } elseif ($cityId > 0) {
                $cityName = City::query()->findOrFail($cityId)->name;
                $title = $cityName;
            }
        }

        $data = [
            'startDate' => $statistics['startDate'],
            'endDate' => $statistics['endDate'],
            'interval' => $granularity,
            'type' => $groupBy,
            'cityId' => $cityId,
            'orgId' => $orgId,
            'schoolId' => $schoolId,
            'display' => $title, // title for chart
            'dataset' => $statistics['chartData'],
            'datasetLastYear' => $statisticsLastYear['chartData'],
            'topFive' => $statistics['top'],
            'moreInfo' => $statistics['moreInfo']
        ];

        return $this->success($data);
    }

    /**
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function topFive($id = null)
    {
        $topFive = $this->statistics->topFiveForCourses($id);
        $city = null;
        if (is_numeric($id)) {
            $city = City::findOrFail($id)->name;
        }

        $data = (object)array(
            'region' => $id ? $city : '',
            'list' => $topFive);
        return $this->success($data);
    }

    /**
     * @param null $granularity
     * @param null $type
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrders($granularity = null, $type = null, $id = null)
    {
        switch ($type) {
            case 'school':
                $school = $this->getSchoolById($id);
                $data = $this->statistics->ordersBySchool($this->transGranularity($granularity), null, $school);
                break;
            case 'city':
                $city = null;
                if (is_numeric($id)) {
                    $city = City::findOrFail($id);
                }
                $data = $this->statistics->ordersByCity($this->transGranularity($granularity), null, $city);
                break;
            case 'organization':
                $organization = $this->getOrganizationById($id);
                $data = $this->statistics->ordersByOrganization($this->transGranularity($granularity), null, $organization);
                break;
            default:
                $data = $this->statistics->orders($this->transGranularity($granularity));
                break;
        }
        return $this->success($data);
    }

    /**
     * @param $id
     * @return mixed|null
     */
    protected function getSchoolById($id)
    {
        if (Auth::user()->isAdmin()) {
            if ($id !== null) {
                return School::findOrFail($id);
            }
        } elseif (Auth::user()->isOrganizationUser()) {
            return School::query()
                ->where('organization_id', '=', Auth::user()->organization_id)
                ->where('id', '=', $id)
                ->get()->first();
        } else {
            return null;
        }
    }

    /**
     * @param $granularity
     * @return string
     */
    protected function transGranularity($granularity)
    {
        switch ($granularity) {
            case 'hourly':
                return StatisticsService::GRANULARITY_HOURLY;
                break;
            case 'daily':
                return StatisticsService::GRANULARITY_DAILY;
                break;
            case 'weekly':
                return StatisticsService::GRANULARITY_WEEKLY;
                break;
            case 'monthly':
                return StatisticsService::GRANULARITY_MONTHLY;
                break;
            case 'yearly':
                return StatisticsService::GRANULARITY_YEARLY;
                break;
            default:
                return StatisticsService::GRANULARITY_MONTHLY;
                break;
        }
    }

    /**
     * @param $id
     * @return null
     */
    protected function getOrganizationById($id)
    {
        if (Auth::user()->isAdmin()) {
            if ($id !== null) {
                return Organization::findOrFail($id);
            }
        } elseif (Auth::user()->isOrganizationUser()) {
            if (Auth::user()->organization_id == $id) {
                return Organization::findOrFail($id);
            } else {
                return Auth::user()->organization()->first();
            }
        } else {
            return null;
        }
    }

    /**
     * @param null $granularity
     * @param null $type
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCourses($granularity = null, $type = null, $id = null)
    {
        $data = [];

        switch ($type) {
            case 'school':
                $school = $this->getSchoolById($id);
                $data = $this->statistics->coursesBySchool($this->transGranularity($granularity), null, $school);
                break;
            case 'city':
                $city = null;
                if (is_numeric($id)) {
                    $city = City::findOrFail($id);
                }
                $data = $this->statistics->coursesByCity($this->transGranularity($granularity), null, $city);

                break;
            case 'organization':
                $organization = $this->getOrganizationById($id);
                $data = $this->statistics->coursesByOrganization($this->transGranularity($granularity), null, $organization);
                break;
            default:
                $data = $this->statistics->courses($this->transGranularity($granularity));
                break;
        }
        return $this->success($data);
    }

    /**
     * @param null $granularity
     * @param null $type
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCancellations($granularity = null, $type = null, $id = null)
    {
        $data = [];

        switch ($type) {
            case 'school':
                $school = $this->getSchoolById($id);
                $data = $this->statistics->cancellationsBySchool($this->transGranularity($granularity), null, $school);
                break;
            case 'city':
                $city = null;
                if (is_numeric($id)) {
                    $city = City::findOrFail($id);
                }
                $data = $this->statistics->cancellationsByCity($this->transGranularity($granularity), null, $city);
                break;
            case 'organization':
                $organization = $this->getOrganizationById($id);
                $data = $this->statistics->cancellationsByOrganization($this->transGranularity($granularity), null, $organization);
                break;
            default:
                $data = $this->statistics->cancellations($this->transGranularity($granularity));
                break;
        }
        return $this->success($data);
    }

    /**
     * @param null $granularity
     * @param null $type
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContact($granularity = null, $type = null, $id = null)
    {
        $data = [];
        switch ($type) {
            case 'school':
                $school = $this->getSchoolById($id);
                $data = $this->statistics->contactBySchool($this->transGranularity($granularity), null, $school);
                break;
            default:
                $data = $this->statistics->contact($this->transGranularity($granularity));
                break;
        }
        return $this->success($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers()
    {
        if (Auth::user()->isAdmin()) {
            return $this->success($this->statistics->users());
        }
        return $this->error();
    }

    /**
     * @param int $connectedIn
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSchools($connectedIn = 2)
    {
        $connected = null;
        if ($connectedIn == 0) {
            $connected = false;
        } elseif ($connectedIn == 1) {
            $connected = true;
        }

        return $this->success($this->statistics->schools($connected));
    }
}
