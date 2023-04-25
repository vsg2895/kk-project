<?php namespace Jakten\Services\Statistics;

use Carbon\Carbon;
use DatePeriod;
use Jakten\Facades\Auth;
use Illuminate\Support\Facades\{DB, Cache};
use Jakten\Models\{City, Order, School, User, Course, ContactRequest, Organization};

/**
 * Class StatisticsService
 * @package Jakten\Services\Statistics
 */
class StatisticsService
{
    const GRANULARITY_DAILY = 0;
    const GRANULARITY_MONTHLY = 1;
    const GRANULARITY_YEARLY = 2;
    const GRANULARITY_WEEKLY = 3;
    const GRANULARITY_HOURLY = 4;

    /**
     * @var Order
     */
    protected $orderModel;

    /**
     * @var School
     */
    protected $schoolModel;

    /**
     * @var User
     */
    protected $userModel;

    /**
     * @var Course
     */
    protected $courseModel;

    /**
     * @var Organization
     */
    protected $organizationModel;

    /**
     * @var City
     */
    protected $cityModel;

    /**
     * @var ContactRequest
     */
    protected $contactRequestModel;

    /**
     * @var bool
     */
    protected $forceRebuild = false;

    /**
     * @var \Carbon\Carbon
     */
    protected $cacheExpiresAt;

    /**
     * @var null|array
     */
    protected $organizationId = null;

    /**
     * @var null|array
     */
    protected $schoolsIds = null;

    /**
     * StatisticsService constructor.
     * @param Order $order
     * @param School $school
     * @param User $user
     * @param Course $course
     * @param Organization $organization
     * @param City $city
     * @param ContactRequest $contactRequest
     */
    public function __construct(Order $order,
                                School $school,
                                User $user,
                                Course $course,
                                Organization $organization,
                                City $city,
                                ContactRequest $contactRequest
    ) {
        $this->orderModel = $order;
        $this->schoolModel = $school;
        $this->userModel = $user;
        $this->courseModel = $course;
        $this->organizationModel = $organization;
        $this->cityModel = $city;
        $this->contactRequestModel = $contactRequest;
        $this->cacheExpiresAt = Carbon::now()->addDay(1)->addHours(6);
    }

    /**
     * Force Statistics cache rebuild.
     *
     * @param string $organizationId
     */
    public function rebuild($organizationId = null)
    {
        $granularitys = [
            SELF::GRANULARITY_HOURLY,
            SELF::GRANULARITY_DAILY,
            SELF::GRANULARITY_WEEKLY,
            SELF::GRANULARITY_MONTHLY,
            SELF::GRANULARITY_YEARLY
        ];
        $citys = City::all();
        $schools = School::all();
        $organizations = Organization::all();
        $this->organizationId = $organizationId;

        foreach ($granularitys as $granularity) {
            $this->ordersBySchool($granularity);
            $this->cancellationsBySchool($granularity);
            $this->coursesBySchool($granularity);
            $this->contactBySchool($granularity);
            foreach ($schools as $school) {
                $this->ordersBySchool($granularity, null, $school);
                $this->cancellationsBySchool($granularity, null, $school);
                $this->coursesBySchool($granularity, null, $school);
                $this->contactBySchool($granularity, null, $school);
            }

            $this->ordersByOrganization($granularity);
            $this->cancellationsByOrganization($granularity);
            foreach ($organizations as $organization) {
                $this->ordersByOrganization($granularity, null, $organization);
                $this->cancellationsByOrganization($granularity, null, $organization);
            }

            $this->ordersByCity($granularity);
            $this->cancellationsByCity($granularity);
            $this->coursesByCity($granularity);
            foreach ($citys as $city) {
                $this->ordersByCity($granularity, null, $city);
                $this->cancellationsByCity($granularity, null, $city);
                $this->coursesByCity($granularity, null, $city);
            }

            $this->orders($granularity);
            $this->cancellations($granularity);
            $this->contact($granularity);
        }

        $this->schools(true);
        $this->schools(false);
        $this->schools(null);
        $this->courses(true);
        $this->courses(false);
        $this->courses(null);
        $this->users();
    }

    /**
     * @param int $granularity
     * @param DatePeriod|null $period
     * @param School|null $school
     * @return mixed
     */
    public function ordersBySchool($granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null, School $school = null)
    {
        return $this->bySchool('=', $granularity, $period, $school);
    }

    /**
     * @param string $operator
     * @param int $granularity
     * @param DatePeriod|null $period
     * @param School|null $school
     * @return mixed
     */
    private function bySchool($operator = '=', $granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null, School $school = null)
    {
        $cacheKey = "StatisticsService" . __FUNCTION__ . $this->cacheResoleGranularity($granularity, $operator);
        if (!is_null($school)) {
            $cacheKey .= $school->id;
        }

        if (Cache::has($cacheKey) && !$this->forceRebuild) {
            $data = Cache::get($cacheKey);
        } else {
            list($select, $groupby) = $this->resoleGranularity($granularity);

            $select[] = 'schools.id';
            $select[] = DB::Raw('count(orders.id) as num');
            $select[] = DB::Raw('sum(items.amount) as amount');
            $groupby[] = 'schools.id';

            $query = $this->orderModel->newQuery();

            $query->rightJoin('schools', 'schools.id', '=', 'orders.school_id');
            $query->leftjoin($this->orderItemsRawSql(), 'items.order_id', '=', 'orders.id');
            if (!is_null($school)) {
                $query->where('schools.id', '=', $school->id);
            }
            if ($this->schoolsIds !== null) {
                $query->whereIn('school_id', $this->schoolsIds);
            }
            $query->where('orders.cancelled', $operator, 1);
            $query->select($select)->groupBy($groupby);
            $data = [];
            foreach ($query->get() as $row) {
                if (!is_null($row['cdate'])) {
                    $data[$row->id][$row->cdate]["orders"] = $row->num;
                    $data[$row->id][$row->cdate]["amount"] = $row->amount;
                }
            }
            if (env('StatisticsServiceCache', "no") === "yes") {
                Cache::put($cacheKey, $data, $this->cacheExpiresAt);
            }
        }
        return $this->periodFilter($granularity, $period, $data);
    }

    /**
     * @param $granularity
     * @param string $operator
     * @return string
     */
    protected function cacheResoleGranularity($granularity = '', $operator = '')
    {
        $this->setOrganizationId();

        switch ($operator) {
            case '=':
                $operatorTrans = 'eq';
                break;
            case '<>':
                $operatorTrans = 'not';
                break;
            default:
                $operatorTrans = '';
                break;
        }

        switch ($granularity) {
            case SELF::GRANULARITY_HOURLY:
                $granularityTrans = "HOURLY";
                break;
            case SELF::GRANULARITY_DAILY:
                $granularityTrans = "DAILY";
                break;
            case SELF::GRANULARITY_WEEKLY:
                $granularityTrans = "WEEKLY";
                break;
            case SELF::GRANULARITY_MONTHLY:
                $granularityTrans = "MONTHLY";
                break;
            case SELF::GRANULARITY_YEARLY:
                $granularityTrans = "YEARLY";
                break;
            default:
                $granularityTrans = "NA";
                break;
        }

        return strtolower($granularityTrans . $operatorTrans . $this->organizationId);
    }

    /**
     * setOrganizationId
     */
    private function setOrganizationId()
    {
        if (Auth::user() !== null) {
            if (Auth::user()->isOrganizationUser()) {
                $this->organizationId = Auth::user()->organization_id;
                $this->setSchoolIds();
            }
        }
    }

    /**
     * setSchoolIds
     */
    private function setSchoolIds()
    {
        if ($this->organizationId !== null) {
            $items = School::query()
                ->where('organization_id', '=', $this->organizationId)
                ->select('id')
                ->get()->all();
            foreach ($items as $item) {
                $this->schoolsIds[] = $item->id;
            }
        } else {
            $this->schoolsIds = null;
        }
    }

    /**
     * @param $granularity
     * @param string $column
     * @return array
     */
    protected function resoleGranularity($granularity, $column = 'orders.created_at')
    {
        switch ($granularity) {
            case SELF::GRANULARITY_HOURLY:
                $select[] = DB::Raw("DATE_FORMAT($column, '%Y-%m-%d-%H') AS cdate");
                $groupby[] = 'cdate';
                break;
            case SELF::GRANULARITY_DAILY:
                $select[] = DB::Raw("DATE_FORMAT($column, '%Y-%m-%d') AS cdate");
                $groupby[] = 'cdate';
                break;
            case SELF::GRANULARITY_WEEKLY:
                $select[] = DB::Raw("DATE_FORMAT($column, '%x-%v') AS cdate");
                $groupby[] = 'cdate';
                break;
            case SELF::GRANULARITY_MONTHLY:
                $select[] = DB::Raw("DATE_FORMAT($column, '%Y-%m') AS cdate");
                $groupby[] = 'cdate';
                break;
            case SELF::GRANULARITY_YEARLY:
                $select[] = DB::Raw("DATE_FORMAT($column, '%Y') AS cdate");
                $groupby[] = 'cdate';
                break;
            case SELF::GRANULARITY_NONE:
                $select = [];
                $groupby = [];
                break;
            default:
                $select[] = DB::Raw("DATE_FORMAT($column, '%Y-%m-%d') AS cdate");
                $groupby[] = 'cdate';
        }
        return [$select, $groupby];
    }

    /**
     * @param string $as
     * @return mixed
     */
    protected function orderItemsRawSql($as = 'items')
    {
        return DB::raw('(SELECT SUM(`amount`) AS amount, `order_id` FROM `order_items` GROUP BY `order_id`) AS ' . $as);
    }

    /**
     * @param int $granularity
     * @param DatePeriod|null $period
     * @param School|null $school
     * @return mixed
     */
    public function cancellationsBySchool($granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null, School $school = null)
    {
        return $this->bySchool('<>', $granularity, $period, $school);
    }

    /**
     * @param int $granularity
     * @param DatePeriod|null $period
     * @param School|null $school
     * @return mixed
     */
    public function coursesBySchool($granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null, School $school = null)
    {
        $cacheKey = "StatisticsService" . __FUNCTION__ . $this->cacheResoleGranularity($granularity);
        if (!is_null($school)) {
            $cacheKey .= $school->id;
        }

        if (Cache::has($cacheKey) && !$this->forceRebuild) {
            $data = Cache::get($cacheKey);
        } else {
            list($select, $groupby) = $this->resoleGranularity($granularity, 'start_time');

            $select[] = DB::Raw('count(school_id) as num');
            $select[] = 'school_id';
            $groupby[] = 'school_id';

            $query = $this->courseModel->newQuery();
            if (!is_null($school)) {
                $query->where('school_id', '=', $school->id);
            }

            if ($this->schoolsIds !== null) {
                $query->whereIn('school_id', $this->schoolsIds);
            }

            $query->select($select)->groupBy($groupby);
            $data = [];
            foreach ($query->get() as $row) {
                if (!is_null($row['cdate'])) {
                    $data[$row->school_id][$row->cdate]["sum"] = $row->num;
                }
            }

            if (env('StatisticsServiceCache', "no") === "yes") {
                Cache::put($cacheKey, $data, $this->cacheExpiresAt);
            }
        }
        return $this->periodFilter($granularity, $period, $data);
    }

    /**
     * @param int $granularity
     * @param DatePeriod|null $period
     * @param School|null $school
     * @return mixed
     */
    public function contactBySchool($granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null, School $school = null)
    {
        $cacheKey = "StatisticsService" . __FUNCTION__ . $this->cacheResoleGranularity($granularity);
        if (!is_null($school)) {
            $cacheKey .= $school->id;
        }

        if (Cache::has($cacheKey) && !$this->forceRebuild) {
            $data = Cache::get($cacheKey);
        } else {
            list($select, $groupby) = $this->resoleGranularity($granularity, 'created_at');
            $select[] = DB::Raw('count(id) as num');
            $select[] = 'school_id';
            $groupby[] = 'school_id';

            $query = $this->contactRequestModel->newQuery();
            if (!is_null($school)) {
                $query->where('school_id', '=', $school->id);
            } else {
                $query->whereNotNull('school_id');
            }

            if ($this->schoolsIds !== null) {
                $query->whereIn('school_id', $this->schoolsIds);
            }

            $query->select($select)->groupBy($groupby);

            $data = [];
            foreach ($query->get() as $row) {
                if (!is_null($row['school_id'])) {
                    $data[$row->cdate][$row->school_id] = $row->num;
                }
            }
            if (env('StatisticsServiceCache', "no") === "yes") {
                Cache::put($cacheKey, $data, $this->cacheExpiresAt);
            }
        }
        return $this->periodFilter($granularity, $period, $data);
    }

    /**
     * @param int $granularity
     * @param DatePeriod|null $period
     * @param Organization|null $organization
     * @return array
     */
    public function ordersByOrganization($granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null, Organization $organization = null)
    {
        return $this->byOrganization('<>', $granularity, $period, $organization);
    }

    /**
     * @param string $operator
     * @param int $granularity
     * @param DatePeriod|null $period
     * @param Organization|null $organization
     * @return mixed
     */
    private function byOrganization($operator = '=', $granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null, Organization $organization = null)
    {
        $cacheKey = "StatisticsService" . __FUNCTION__ . $this->cacheResoleGranularity($granularity, $operator);
        if (!is_null($organization)) {
            $cacheKey .= $organization->id;
        }

        if (Cache::has($cacheKey) && !$this->forceRebuild) {
            $data = Cache::get($cacheKey);
        } else {
            list($select, $groupby) = $this->resoleGranularity($granularity);

            $select[] = 'schools.organization_id';
            $select[] = DB::Raw('count(orders.id) as num');
            $select[] = DB::Raw('sum(items.amount) as amount');
            $groupby[] = 'schools.organization_id';

            $query = $this->orderModel->newQuery();

            $query->rightJoin('schools', 'schools.id', '=', 'orders.school_id');
            $query->leftjoin($this->orderItemsRawSql(), 'items.order_id', '=', 'orders.id');

            if ($this->organizationId !== null) {
                $query->where('schools.organization_id', '=', $this->organizationId);
            } elseif (!is_null($organization)) {
                $query->where('schools.organization_id', '=', $organization->id);
            }
            $query->where('orders.cancelled', $operator, 1);
            $query->select($select)->groupBy($groupby);
            $data = [];
            foreach ($query->get() as $row) {
                if (!is_null($row['cdate'])) {
                    $data[$row->organization_id][$row->cdate]["orders"] = $row->num;
                    $data[$row->organization_id][$row->cdate]["amount"] = $row->amount;
                }
            }
            if (env('StatisticsServiceCache', "no") === "yes") {
                Cache::put($cacheKey, $data, $this->cacheExpiresAt);
            }
        }
        return $this->periodFilter($granularity, $period, $data);
    }

    /**
     * @param int $granularity
     * @param DatePeriod|null $period
     * @param Organization|null $organization
     * @return mixed
     */
    public function cancellationsByOrganization($granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null, Organization $organization = null)
    {
        return $this->byOrganization('=', $granularity, $period, $organization);
    }

    /**
     * @param int $granularity
     * @param DatePeriod|null $period
     * @param City|null $city
     * @return array
     */
    public function ordersByCity($granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null, City $city = null)
    {
        return $this->byCity('<>', $granularity, $period, $city);
    }

    /**
     * @param string $operator
     * @param int $granularity
     * @param DatePeriod|null $period
     * @param City|null $city
     * @return mixed
     */
    private function byCity($operator = '=', $granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null, City $city = null)
    {
        $cacheKey = "StatisticsService" . __FUNCTION__ . $this->cacheResoleGranularity($granularity, $operator);
        if (!is_null($city)) {
            $cacheKey .= $city->id;
        }


        if (Cache::has($cacheKey) && !$this->forceRebuild) {
            $data = Cache::get($cacheKey);
        } else {
            list($select, $groupby) = $this->resoleGranularity($granularity);

            $select[] = 'schools.city_id';
            $select[] = DB::Raw('count(orders.id) as num');
            $select[] = DB::Raw('sum(items.amount) as amount');
            $groupby[] = 'schools.city_id';

            $query = $this->orderModel->newQuery();

            $query->rightJoin('schools', 'schools.id', '=', 'orders.school_id');
            $query->leftjoin($this->orderItemsRawSql(), 'items.order_id', '=', 'orders.id');
            if ($this->schoolsIds !== null) {
                $query->whereIn('schools.id', $this->schoolsIds);
            }
            if (!is_null($city)) {
                $query->where('schools.city_id', '=', $city->id);
            }
            $query->where('orders.cancelled', $operator, 1);
            $query->select($select)->groupBy($groupby);
            $data = [];
            foreach ($query->get() as $row) {
                if (!is_null($row['cdate'])) {
                    $data[$row->city_id][$row->cdate]["orders"] = $row->num;
                    $data[$row->city_id][$row->cdate]["amount"] = $row->amount;
                }
            }

            if (env('StatisticsServiceCache', "no") === "yes") {
                Cache::put($cacheKey, $data, $this->cacheExpiresAt);
            }
        }
        return $this->periodFilter($granularity, $period, $data);
    }

    /**
     * @param int $granularity
     * @param DatePeriod|null $period
     * @param City|null $city
     * @return mixed
     */
    public function cancellationsByCity($granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null, City $city = null)
    {
        return $this->byCity('=', $granularity, $period, $city);
    }

    /**
     * @param int $granularity
     * @param DatePeriod|null $period
     * @param City|null $city
     * @return mixed
     */
    public function coursesByCity($granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null, City $city = null)
    {
        $cacheKey = "StatisticsService" . __FUNCTION__ . $this->cacheResoleGranularity($granularity);
        if (!is_null($city)) {
            $cacheKey .= $city->id;
        }

        if (Cache::has($cacheKey) && !$this->forceRebuild) {
            $data = Cache::get($cacheKey);
        } else {
            list($select, $groupby) = $this->resoleGranularity($granularity, 'start_time');

            $select[] = DB::Raw('count(city_id) as num');
            $select[] = 'city_id';
            $groupby[] = 'city_id';

            $query = $this->courseModel->newQuery();

            if (!is_null($city)) {
                $query->where('courses.city_id', '=', $city->id);
            }

            if ($this->schoolsIds !== null) {
                $query->whereIn('school_id', $this->schoolsIds);
            }

            $query->select($select)->groupBy($groupby);
            $data = [];
            foreach ($query->get() as $row) {
                if (!is_null($row['cdate'])) {
                    $data[$row->city_id][$row->cdate]["sum"] = $row->num;
                }
            }
            if (env('StatisticsServiceCache', "no") === "yes") {
                Cache::put($cacheKey, $data, $this->cacheExpiresAt);
            }
        }
        return $this->periodFilter($granularity, $period, $data);
    }

    /**
     * @param int $granularity
     * @param DatePeriod|null $period
     * @return array
     */
    public function orders($granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null)
    {
        return $this->ordersCancellations('<>', $granularity, $period);
    }

    /**
     * @param string $operator
     * @param int $granularity
     * @param DatePeriod|null $period
     * @return mixed
     */
    private function ordersCancellations($operator = '=', $granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null)
    {
        $cacheKey = "StatisticsService" . __FUNCTION__ . $this->cacheResoleGranularity($granularity, $operator);


        if (Cache::has($cacheKey) && !$this->forceRebuild) {
            $data = Cache::get($cacheKey);
        } else {
            list($select, $groupby) = $this->resoleGranularity($granularity);

            $select[] = DB::Raw('count(orders.id) as num');
            $select[] = DB::Raw('sum(items.amount) as amount');

            $query = $this->orderModel->newQuery();

            $query->rightJoin('schools', 'schools.id', '=', 'orders.school_id');
            $query->leftjoin($this->orderItemsRawSql(), 'items.order_id', '=', 'orders.id');
            $query->where('orders.cancelled', $operator, 1);
            if ($this->schoolsIds !== null) {
                $query->whereIn('school_id', $this->schoolsIds);
            }
            $query->select($select)->groupBy($groupby);
            $data = [];
            foreach ($query->get() as $row) {
                if (!is_null($row['cdate'])) {
                    $ntr = $this->nullToRand($row->city_id);
                    $data[$ntr][$row->cdate]["orders"] = $row->num;
                    $data[$ntr][$row->cdate]["amount"] = $row->amount;
                }
            }
            if (env('StatisticsServiceCache', "no") === "yes") {
                Cache::put($cacheKey, $data, $this->cacheExpiresAt);
            }
        }
        return $this->periodFilter($granularity, $period, $data);
    }

    /**
     * @param $id
     * @return int
     */
    private function nullToRand($id)
    {
        if ($id === null) {
            return 0;
        }
        return $id;
    }

    /**
     * @param int $granularity
     * @param DatePeriod|null $period
     * @return mixed
     */
    public function cancellations($granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null)
    {
        return $this->ordersCancellations('=', $granularity, $period);
    }

    /**
     * @param int $granularity
     * @param DatePeriod|null $period
     * @return mixed
     */
    public function contact($granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null)
    {
        $cacheKey = "StatisticsService" . __FUNCTION__ . $this->cacheResoleGranularity($granularity);


        if (Cache::has($cacheKey) && !$this->forceRebuild) {
            $data = Cache::get($cacheKey);
        } else {
            list($select, $groupby) = $this->resoleGranularity($granularity, 'created_at');
            $select[] = DB::Raw('count(id) as num');

            $query = $this->contactRequestModel->newQuery();
            if ($this->schoolsIds !== null) {
                $query->whereIn('school_id', $this->schoolsIds);
            }
            $query->whereNotNull('school_id');
            $query->select($select)->groupBy($groupby);
            $data = [];
            foreach ($query->get() as $row) {
                $data[$row->cdate][0] = $row->num;
            }
            if (env('StatisticsServiceCache', "no") === "yes") {
                Cache::put($cacheKey, $data, $this->cacheExpiresAt);
            }
        }
        return $this->periodFilter($granularity, $period, $data);
    }

    /**
     * @param bool $connected
     * @return int
     */
    public function schools($connected = true)
    {
        $this->setOrganizationId();
        $cacheKey = "StatisticsService" . __FUNCTION__ . $this->cacheResoleGranularity();
        if ($connected !== null) {
            if ($connected === true) {
                $cacheKey .= "true";
            } else {
                $cacheKey .= "false";
            }
        }

        if (Cache::has($cacheKey) && !$this->forceRebuild) {
            $data = Cache::get($cacheKey);
        } else {
            $query = $this->schoolModel->newQuery();
            if ($connected !== null) {
                if ($connected === true) {
                    $query->whereNotNull('organization_id');
                } else {
                    $query->whereNull('organization_id');
                }
            }
            if ($this->organizationId !== null) {
                $query->where('organization_id', '=', $this->organizationId);
            }
            $data = ["num" => $query->count()];
            if (env('StatisticsServiceCache', "no") === "yes") {
                Cache::put($cacheKey, $data, $this->cacheExpiresAt);
            }
        }
        return $data;
    }

    /**
     * @param bool $passed
     * @return int
     */
    public function courses($passed = true)
    {
        $this->setOrganizationId();
        $cacheKey = "StatisticsService" . __FUNCTION__ . $this->cacheResoleGranularity();
        if ($passed !== null) {
            if ($passed === true) {
                $cacheKey .= "true";
            } else {
                $cacheKey .= "false";
            }
        }

        if (Cache::has($cacheKey) && !$this->forceRebuild) {
            $data = Cache::get($cacheKey);
        } else {
            $query = $this->courseModel->newQuery();
            if ($this->schoolsIds !== null) {
                $query->whereIn('school_id', $this->schoolsIds);
            }

            if ($passed !== null) {
                if ($passed === true) {
                    $query->where('start_time', '>=', Carbon::now());
                } else {
                    $query->where('start_time', '<=', Carbon::now());
                }
            }
            $data = ["num" => $query->count()];
            if (env('StatisticsServiceCache', "no") === "yes") {
                Cache::put($cacheKey, $data, $this->cacheExpiresAt);
            }
        }
        return $data;
    }

    /**
     * @return array
     */
    public function users()
    {
        $cacheKey = "StatisticsService" . __FUNCTION__;

        if (Cache::has($cacheKey) && !$this->forceRebuild) {
            $data = Cache::get($cacheKey);
        } else {
            $select[] = DB::Raw('count(id) as num');
            $select[] = 'role_id';
            $groupby[] = 'role_id';

            $query = $this->userModel->newQuery();
            $query->select($select)->groupBy($groupby);
            $data = [];
            foreach ($query->get() as $row) {
                if (!is_null($row['role_id'])) {
                    $data[$row->role_id]["sum"] = $row->num;
                }
            }
            if (env('StatisticsServiceCache', "no") === "yes") {
                Cache::put($cacheKey, $data, $this->cacheExpiresAt);
            }
        }
        return $data;
    }

    /**
     * @return $this
     */
    public function setForceRebuild()
    {
        $this->forceRebuild = true;
        return $this;
    }

    /**
     * @param int $granularity
     * @param DatePeriod|null $period
     * @param Organization|null $organization
     * @return mixed
     */
    public function coursesByOrganization($granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null, Organization $organization = null)
    {
        $cacheKey = "StatisticsService" . __FUNCTION__ . $this->cacheResoleGranularity($granularity);
        if (!is_null($organization)) {
            $cacheKey .= $organization->id;
        }

        if (Cache::has($cacheKey) && !$this->forceRebuild) {
            $data = Cache::get($cacheKey);
        } else {
            list($select, $groupby) = $this->resoleGranularity($granularity, 'start_time');

            $select[] = DB::Raw('count(school_id) as num');
            $select[] = DB::Raw('organizations.id as oid');
            $groupby[] = 'oid';

            $query = $this->courseModel->newQuery();
            $query->leftJoin('schools', 'schools.id', '=', 'courses.school_id');
            $query->leftJoin('organizations', 'organizations.id', '=', 'schools.organization_id');
            if (!is_null($organization)) {
                $query->where('schools.organization_id', '=', $organization->id);
            }
            $query->select($select)->groupBy($groupby);

            $data = [];
            foreach ($query->get() as $row) {
                if (!is_null($row['cdate'])) {
                    $data[$row->oid][$row->cdate]["sum"] = $row->num;
                }
            }
            if (env('StatisticsServiceCache', "no") === "yes") {
                Cache::put($cacheKey, $data, $this->cacheExpiresAt);
            }
        }
        return $this->periodFilter($granularity, $period, $data);
    }

    // TODO: Write filter.
    protected function periodFilter($granularity = SELF::GRANULARITY_MONTHLY, DatePeriod $period = null, $data)
    {
        return $data;
    }

    /**
     * @return int
     */
    public function coursesCount(){
        return Course::query()
            ->where('deleted_at', null)
            ->count();
    }

    /**
     * @param null $cityId
     * @return array
     */
    public function topFiveForCourses($cityId = null)
    {
        $query = Course::query()
            ->where('deleted_at', null);

        if ($cityId !== null) {
            $query->where('city_id', $cityId);
        }

        $total = $query->count();

        if ($cityId !== null) {
            $query->select('school_id', DB::raw('count(*) as total'))
                ->groupBy('school_id');
        } else {
            $query->select('city_id', DB::raw('count(*) as total'))
                ->groupBy('city_id');
        }

        $groups = $query->orderBy('total', 'desc')
                ->get();

        $topFive = [];

        foreach ($groups as $group) {
            $id = $cityId? $group->school_id : $group->city_id;
            $name = $cityId? School::find($group->school_id)->name : City::find($group->city_id)->name;
            $topFive[] = (object)array(
                'id' => $id,
                'name' => $name,
                'count' => $group->total,
                'share' => $group->total/$total
            );
        }

        return $topFive;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function topFiveForContacts()
    {
        return $this->contactRequestModel
            ->newQuery()
            ->whereNotNull('school_id')
            ->join('schools', 'school_id', '=', 'schools.id')
            ->select('school_id', 'schools.name as name', DB::raw('count(*) as count'))
            ->groupBy('school_id')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * @return \Illuminate\Support\Collection|static
     */
    public function userTypes()
    {
        $types = User::query()
            ->select('role_id', DB::raw('count(*) as count'))
            ->groupBy('role_id')
            ->get();

        return $types->map(function ($type) {
            switch ($type->role_id) {
                case 3:
                    $type_name = 'Admin';
                    break;
                case 2:
                    $type_name = 'Trafiklärare';
                    break;
                default:
                    $type_name = 'Elever';
                    break;
            }
            return [$type_name, $type->count];
        });
    }

    /**
     * @param $orgId
     * @return int
     */
    public function countCoursesByOrg($orgId, $schoolId = null){
        $query = Course::query()
            ->whereNotNull('school_id')
            ->join('schools', 'school_id', '=', 'schools.id')
            ->where('schools.organization_id','=',$orgId);

        return !is_null($schoolId)
            ? $query->where('schools.id','=',$schoolId)->get()->count()
            : $query->get()->count();
    }

    /**
     * @param $orgId
     * @return int
     */
    public function countContactsByOrg($orgId, $schoolId = null){

        $query = $this->contactRequestModel
            ->newQuery()
            ->whereNotNull('school_id')
            ->join('schools', 'school_id', '=', 'schools.id')
            ->where('schools.organization_id','=',$orgId);

        return !is_null($schoolId)
            ? $query->where('schools.id','=',$schoolId)->get()->count()
            : $query->get()->count();

    }

    /**
     * @param int|null $organizationId
     * @param City $city
     * @param int $granularity
     * @param DatePeriod|null $period
     * @return mixed
     */
    public function coursesAverage(int $organizationId = null, City $city = null, $schoolId = null, $granularity = SELF::GRANULARITY_HOURLY, DatePeriod $period = null)
    {
        $cacheKey = "StatisticsServiceAverage" . __FUNCTION__ . $this->cacheResoleGranularity($granularity);
        if (!is_null($organizationId)) {
            $cacheKey .= $organizationId;
        }

        if (!is_null($city)) {
            $cacheKey .= $city->id;
        }

        if (Cache::has($cacheKey) && !$this->forceRebuild) {
            $data = Cache::get($cacheKey);
        } else {
            $select[] = DB::Raw('ROUND(avg(price), 2) as avg');
            $select[] = DB::Raw("CONCAT('vehicle_segments.', LOWER(vehicle_segments.name)) as name");
            $select[] = DB::Raw("vehicle_segments.id as id");
            $groupby[] = 'courses.vehicle_segment_id';

            $query = DB::table('courses');

            $query->join('vehicle_segments', 'vehicle_segments.id', '=', 'courses.vehicle_segment_id');
            $query->join('order_items', 'order_items.course_id', '=', 'courses.id');

            if (!is_null($organizationId)) {
                $query->leftJoin('schools', 'schools.id', '=', 'courses.school_id');
                $query->leftJoin('organizations', 'organizations.id', '=', 'schools.organization_id');

                $query->where('schools.organization_id', '=', $organizationId);
            }

            if (!is_null($city)) {

                $query->where("city_id", $city->id);
            } else {
                $query->where("courses.school_id", $schoolId);
            }

            $query->whereBetween('courses.start_time', [(new Carbon)->subDays(100)->toDateString(), (new Carbon)->now()->addMonth(2)->toDateString()]);
            $query->select($select)->groupBy($groupby);
            $data = $query->get();

            if (env('StatisticsServiceCache', "no") === "yes") {
                Cache::put($cacheKey, $data, $this->cacheExpiresAt);
            }
        }
        return $this->periodFilter($granularity, $period, $data);
    }

    public function bookWindowInfo(int $organizationId = null, City $cityId = null, $schoolId = null, $granularity = SELF::GRANULARITY_HOURLY, DatePeriod $period = null)
    {
        $cacheKey = "StatisticsServiceAverage" . __FUNCTION__ . $this->cacheResoleGranularity($granularity);
        if (!is_null($organizationId)) {
            $cacheKey .= $organizationId;
        }

        if (!is_null($cityId)) {
            $cacheKey .= $cityId;
        }

        if (Cache::has($cacheKey) && !$this->forceRebuild) {
            $dataInfo = Cache::get($cacheKey);
        } else {
            $select[] = DB::Raw("COUNT(order_items.id) as count");
            $select[] = DB::Raw("courses.start_time");
            $select[] = DB::Raw("order_items.created_at");
            $select[] = DB::Raw("IF(
                DATEDIFF(courses.start_time, order_items.created_at) < 3, 'Less then 3 days - ', 
                    IF(DATEDIFF(courses.start_time, order_items.created_at) > 3 AND DATEDIFF(courses.start_time, order_items.created_at) < 11 , '4-10 days - ', 
                    IF(DATEDIFF(courses.start_time, order_items.created_at) > 10 AND DATEDIFF(courses.start_time, order_items.created_at) < 21 , '10-20 days - ', 'More then 20 days - '))) as days_between");
            $groupby[] = 'days_between';

            $query = DB::table('order_items');

            $query->join('courses', 'order_items.course_id', '=', 'courses.id');

            if (!is_null($organizationId)) {
                $query->leftJoin('schools', 'schools.id', '=', 'courses.school_id');
                $query->leftJoin('organizations', 'organizations.id', '=', 'schools.organization_id');

                $query->where('schools.organization_id', '=', $organizationId);
            }

            if (!is_null($schoolId)) {
                $query->where("order_items.school_id", $schoolId);
            }
            $query->whereBetween('order_items.created_at', [(new Carbon)->subDays(100)->toDateTimeString(), (new Carbon)->now()->addMonth(2)->toDateTimeString()]);

            $data = $query->select($select)->groupBy($groupby)->get();
            $dataInfo = [];

            foreach ($data as $item) {
                $dataInfo[] = [$item->days_between, $item->count];
            }

            if (env('StatisticsServiceCache', "no") === "yes") {
                Cache::put($cacheKey, $dataInfo, $this->cacheExpiresAt);
            }
        }

        return $this->periodFilter($granularity, $period, $dataInfo);

    }
}
