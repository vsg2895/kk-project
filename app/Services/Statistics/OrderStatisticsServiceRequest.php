<?php namespace Jakten\Services\Statistics;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jakten\Models\{City, Order, School, Organization};

/**
 * Class OrderStatsServiceRequest
 * @package Jakten\Services\Statistics
 */
abstract class OrderStatsServiceRequest
{
    protected $start;
    protected $end;
    public $granularity;
    protected $granularityGroupFormatStr;
    protected $granularityLabelFormatStr;
    public $type;
    public $cityId;
    public $schoolId;
    public $orgId;
    protected $query;
    
    public function __construct($startDate, $endDate, $granularity, $cityId, $orgId, $schoolId)
    {
        $this->granularity = $granularity;
        $this->setDates($startDate, $endDate);
        $this->cityId = $cityId;
        $this->orgId = $orgId;
        $this->schoolId = $schoolId;
    }

    abstract public function chartTitle();
    abstract public function topFive();

    /**
     * @param $startDate
     * @param $endDate
     */
    private function setDates($startDate, $endDate)
    {
        $start = Carbon::createFromFormat('Y-m-d', $startDate);
        $end = Carbon::createFromFormat('Y-m-d', $endDate);
        switch ($this->granularity) {
            case 'yearly':
                $this->start = $start->startOfYear();
                $this->end = $end->endOfYear();
                $this->granularityGroupFormatStr = 'Y';
                $this->granularityLabelFormatStr = 'Y';
                break;
            case 'monthly':
                $this->start = $start->startOfMonth();
                $this->end = $end->endOfMonth();
                $this->granularityGroupFormatStr = 'Y-m';
                $this->granularityLabelFormatStr = 'F';
                break;
            case 'weekly':
                $this->start = $start->startOfWeek();
                $this->end = $end->endOfWeek();
                $this->granularityGroupFormatStr = 'Y-W';
                $this->granularityLabelFormatStr = 'W';
                break;
            default: // for daily
                $this->start = $start->startOfDay();
                $this->end = $end->endOfDay();
                $this->granularityGroupFormatStr = 'Y-m-d';
                $this->granularityLabelFormatStr = 'Y-m-d';
                break;
        }
    }

    /**
     * initQuery
     */
    private function initQuery()
    {
        $query = Order::query()
            ->where('orders.created_at', '>=', $this->start->toDateTimeString())
            ->where('orders.created_at', '<=', $this->end->toDateTimeString())
            ->join('schools', 'orders.school_id', '=', 'schools.id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id');
        if ($this->cityId > 0) {
            $query->where('city_id', $this->cityId);
        }
        if ($this->orgId > 0) {
            $query->where('organization_id', $this->orgId);
        }
        if ($this->schoolId > 0) {
            $query->where('schools.id', $this->schoolId);
        }

        $this->query = $query;
    }

    /**
     * @return object
     */
    public function chartData()
    {
        $this->initQuery();

        $this->query->select(
            'orders.id as order_id',
            'schools.city_id', 
            'schools.organization_id', 
            'orders.school_id', 
            'orders.cancelled',
            'orders.created_at',
            DB::raw('(order_items.amount * order_items.quantity) AS total')
        );
        
        $groups = $this->query->get()
            ->groupBy(function ($order, $key) {
                return $order->created_at->format($this->granularityGroupFormatStr);
            })
            ->sortBy(function ($group) {
                return $group->first()->created_at;
            })
            ->map(function ($group) {
                $dateStr = $group->first()->created_at->format($this->granularityLabelFormatStr); 
                return (object)[
                    'label' => $this->granularity === 'monthly'? substr($dateStr, 0, 3) : $dateStr,
                    'booked' => $group->count(),
                    'cancelled' => $group->where('cancelled', 1)->count(),
                    'turnover' => $group->sum('total')
                ];
            });
            
        return (object)[
            'labels' => $groups->map(function($group){ return $group->label; })->flatten(),
            'booked' => $groups->map(function($group){ return $group->booked; })->flatten(),
            'cancelled' => $groups->map(function($group){ return $group->cancelled; })->flatten(),
            'turnover' => $groups->map(function($group){ return $group->turnover; })->flatten()
        ];
    }

    /**
     * @return mixed
     */
    protected function getTopFive()
    {
        $this->initQuery();
        
        $this->query->select(
            'orders.id as order_id',
            'schools.city_id', 
            'schools.organization_id', 
            'orders.school_id', 
            DB::raw('COUNT(*) AS booked'), 
            DB::raw('SUM(orders.cancelled) AS cancelled'),
            DB::raw('SUM(order_items.amount * order_items.quantity) AS turnover')
        );
        
        if ($this->cityId > 0 && $this->orgId > 0) {
            $this->query->groupBy('school_id');
        } elseif ($this->cityId > 0) {
            $this->query->groupBy('organization_id');
        } else {
            $this->query->groupBy('city_id');
        }
        
        $raw = $this->query->orderBy('booked', 'desc')->get();
        return $raw->take(5)->map(function ($row) {
            return (object)[
                'cityId' => $row->city_id,
                'orgId' => $row->organization_id,
                'schoolId' => $row->school_id,
                'name' => '',
                'booked' => $row->booked,
                'cancelled' => $row->cancelled,
                'turnover' => number_format(floatval($row->turnover), 2, ',', ' '),
            ];
        });
    }

    /**
     * @return mixed
     */
    public function startDate(){
        return $this->start->format('Y-m-d');
    }

    /**
     * @return mixed
     */
    public function endDate(){
        return $this->end->format('Y-m-d');
    }

    /**
     * @param $isOrgUser
     * @param $startDate
     * @param $endDate
     * @param $granularity
     * @param $type
     * @param $cityId
     * @param $orgId
     * @param $schoolId
     * @return OrderStatsByCityRequest|OrderStatsByOrgRequest|OrderStatsBySchoolRequest
     */
    public static function factory($isOrgUser, $startDate, $endDate, $granularity, $type, $cityId, $orgId, $schoolId){
        switch($type){
            case 'school':
                $request = new OrderStatsBySchoolRequest($startDate, $endDate, $granularity, $cityId, $orgId, $schoolId);
                break;
            case 'organization':
                $schoolId = -1;
                if($isOrgUser){
                    $request = new OrderStatsBySchoolRequest($startDate, $endDate, $granularity, $cityId, $orgId, $schoolId);
                } else {
                    $orgId = -1;
                    $request = new OrderStatsByOrgRequest($startDate, $endDate, $granularity, $cityId, $orgId, $schoolId);
                }
                break;
            default: // for city
                $schoolId = -1;
                $orgId = $isOrgUser? $orgId : -1;
                $cityId = -1;
                $request = new OrderStatsByCityRequest($startDate, $endDate, $granularity, $cityId, $orgId, $schoolId);
                break;
        }
        return $request;
    }
}

class OrderStatsByCityRequest extends OrderStatsServiceRequest
{
    /**
     * OrderStatsByCityRequest constructor.
     * @param $startDate
     * @param $endDate
     * @param $granularity
     * @param $cityId
     * @param $orgId
     * @param $schoolId
     */
    public function __construct($startDate, $endDate, $granularity, $cityId, $orgId, $schoolId)
    {
        parent::__construct($startDate, $endDate, $granularity, $cityId, $orgId, $schoolId);
        $this->type = 'city';
    }

    /**
     * @return string
     */
    public function chartTitle()
    {
        return 'Sverige';
    }

    /**
     * @return mixed
     */
    public function topFive()
    {
        $topFive = $this->getTopFive();

        foreach ($topFive as $row) {
            $row->name = City::findOrFail($row->cityId)->name;
        }
        
        return $topFive;
    }
}

class OrderStatsByOrgRequest extends OrderStatsServiceRequest
{
    /**
     * OrderStatsByOrgRequest constructor.
     * @param $startDate
     * @param $endDate
     * @param $granularity
     * @param $cityId
     * @param $orgId
     * @param $schoolId
     */
    public function __construct($startDate, $endDate, $granularity, $cityId, $orgId, $schoolId)
    {
        parent::__construct($startDate, $endDate, $granularity, $cityId, $orgId, $schoolId);
        $this->type = 'organization';
    }

    /**
     * @return string
     */
    public function chartTitle()
    {
        return $this->cityId > 0? City::findOrFail($this->cityId)->name : 'Sverige';
    }

    /**
     * @return mixed
     */
    public function topFive()
    {
        $topFive = $this->getTopFive();

        foreach ($topFive as $row) {
                $row->name = Organization::findOrFail($row->orgId)->name;
        }

        return $topFive;
    }
}

class OrderStatsBySchoolRequest extends OrderStatsServiceRequest
{
    /**
     * OrderStatsBySchoolRequest constructor.
     * @param $startDate
     * @param $endDate
     * @param $granularity
     * @param $cityId
     * @param $orgId
     * @param $schoolId
     */
    public function __construct($startDate, $endDate, $granularity, $cityId, $orgId, $schoolId)
    {
        parent::__construct($startDate, $endDate, $granularity, $cityId, $orgId, $schoolId);
        
        $this->type = 'school';
    }

    /**
     * @return string
     */
    public function chartTitle()
    {
        if($this->schoolId > 0) {
            return School::findOrFail($this->schoolId)->name;
        }
        $orgStr = $this->orgId > 0 ? Organization::findOrFail($this->orgId)->name . ' i ' : '';
        $cityStr = $this->cityId > 0 ? City::findOrFail($this->cityId)->name : 'Sverige';
        return $orgStr . $cityStr;
    }

    /**
     * @return mixed
     */
    public function topFive()
    {
        $topFive = $this->getTopFive();

        foreach ($topFive as $row) {
            $row->name = School::findOrFail($row->schoolId)->name;
        }

        return $topFive;
    }
}
