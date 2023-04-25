<?php namespace Jakten\Services\Statistics;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Jakten\Models\Addon;
use Jakten\Repositories\OrderRepository;

/**
 * Class OrderStatisticsService
 * @package Jakten\Services\Statistics
 */
class OrderStatisticsService
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var Carbon
     */
    public $startDate;

    /**
     * @var Carbon
     */
    public $endDate;

    /**
     * @var $granularityGroupFormat
     */
    private $granularityGroupFormat;

    /**
     * @var $granularityLabelFormat
     */
    private $granularityLabelFormat;

    /**
     * @var $granularity
     */
    private $granularity;

    /**
     * OrderStatisticsService constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param $groupBy
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param Collection $data
     * @return array
     */
    public function getStatistics($groupBy, Carbon $startDate, Carbon $endDate, Collection $data)
    {
        $this->granularity = $data->get('granularity');
        $this->setDates($startDate, $endDate);
        $topItems = null;
        $itemsMoreInfo = null;
        switch ($groupBy) {
            case 'school':
                $topItems = $this->schoolOrderStatistics($data);
                $itemsMoreInfo = $this->schoolOrderStatisticsMoreInfo($data);
                break;
            case 'organization':
                $topItems = $this->organizationOrderStatistics($data);
                $itemsMoreInfo = $this->organizationOrderStatisticsMoreInfo($data);
                break;
            case 'city':
                $topItems = $this->cityOrderStatistics($data);
                $itemsMoreInfo = $this->cityOrderStatisticsMoreInfo($data);
                break;
        }

        return ['top' => $topItems, 'moreInfo' => $itemsMoreInfo, 'chartData' => $this->chartData($data), 'startDate' => $this->startDate->toDateString(), 'endDate' => $this->endDate->toDateString()];
    }

    /**
     * @param Collection $data
     * @return mixed
     */
    private function schoolOrderStatistics(Collection $data)
    {//todo check gift card orders
        $addons = Addon::pluck('name')->toArray();
        $addons = "('" . implode("','", $addons) . "')";

        $query = $this->baseQuery();
        $query->groupBy('schools.id')
            ->select([
                'schools.name',
                DB::raw('schools.id as schoolId'),
                DB::raw("SUM(case when order_items.course_id is not null and order_items.cancelled = 0 then 1 else 0 end) 
                        + SUM(IF(order_items.custom_addon_id IS NOT NULL && order_items.cancelled = false, 1, 0))
                        + COUNT(IF(order_items.type IN $addons && order_items.cancelled = false, orders.id, NULL)) as booked"),
                DB::raw("SUM(case when order_items.course_id is not null and (order_items.cancelled = 1 or order_items.credited = 1) then 1 else 0 end) 
                        + SUM(IF(order_items.custom_addon_id IS NOT NULL && order_items.cancelled = true, 1, 0))
                        + COUNT(IF(order_items.type IN $addons && order_items.cancelled = true, orders.id, NULL)) as cancelled"),
                DB::raw('SUM(case when order_items.cancelled = 0 and order_items.credited = 0 then order_items.amount * order_items.quantity else 0 end) as turnover')
            ])
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->orderBy('booked', 'desc');

        if ($data->get('organizationId') > 0) {
            $query->where('schools.organization_id', $data->get('organizationId'));
        }

        $topSchools = $query->get();
        return $topSchools->toArray();
    }


    private function schoolOrderStatisticsMoreInfo(Collection $data)
    {
        $addons = Addon::pluck('name')->toArray();
        $addons = "('" . implode("','", $addons) . "')";

        $query = $this->baseQuery();
        $query->select([
                'schools.name',
                DB::raw('schools.id as schoolId'),
                DB::raw('courses.vehicle_segment_id as segmentId'),
                DB::raw('vehicle_segments.name as segment_name'),
                DB::raw('COUNT(IF(order_items.course_id IS NOT NULL && order_items.cancelled = false, orders.id, NULL)) as order_count'),
                DB::raw('COUNT(IF(order_items.course_id IS NOT NULL && order_items.cancelled = true, orders.id, NULL)) as cancelled_order_count'),
                DB::raw('SUM(case when order_items.cancelled = false and order_items.credited = false then order_items.amount * order_items.quantity else 0 end) as turnover'),

                DB::raw("SUM(case when (order_items.custom_addon_id IS NOT NULL OR order_items.type IN $addons) 
                    and (order_items.cancelled = false and order_items.credited = false) then order_items.amount * order_items.quantity else 0 end) as paket_turnover"),

                DB::raw("SUM(IF(order_items.custom_addon_id IS NOT NULL && order_items.cancelled = false, 1, 0))
                 + COUNT(IF(order_items.type IN $addons && order_items.cancelled = false, orders.id, NULL)) as paket_count"),
                DB::raw("SUM(IF(order_items.custom_addon_id IS NOT NULL && order_items.cancelled = true, 1, 0))
                 + COUNT(IF(order_items.type IN $addons && order_items.cancelled = true, orders.id, NULL)) as cancelled_paket_count"),
            ])
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('courses','order_items.course_id', '=', 'courses.id')
            ->leftJoin('vehicle_segments', 'vehicle_segments.id', '=', 'courses.vehicle_segment_id')
            ->groupBy(['schools.id', 'courses.vehicle_segment_id']);

        if ($data->get('organizationId') > 0) {
            $query->where('schools.organization_id', $data->get('organizationId'));
        }

        return $query->get()->map(function ($item) {
            $item->segment_name = __('vehicle_segments.' . strtolower($item->segment_name));

            return $item;
        })->groupBy(['name'])->toArray();

    }

    /**
     * @param Collection $data
     * @return mixed
     */
    private function organizationOrderStatistics(Collection $data)
    {//todo add addons count in query
        $query = $this->baseQuery();
        $query->join('organizations', 'schools.organization_id', '=', 'organizations.id')
            ->groupBy('organizations.id')
            ->select([
                'organizations.name',
                DB::raw('organizations.id as orgId'),
                DB::raw('SUM(case when order_items.course_id is not null then 1 else 0 end) as booked'),
                DB::raw('SUM(case when order_items.course_id is not null and (order_items.cancelled = 1 or order_items.credited = 1) then 1 else 0 end) as cancelled'),
                DB::raw('SUM(case when order_items.cancelled = 0 and order_items.credited = 0 then order_items.amount * order_items.quantity else 0 end) as turnover')
            ])
            ->orderBy('booked', 'desc');

        if ($data->get('cityId') > 0) {
            $query->where('schools.city_id', $data->get('cityId'));
        }

        $topOrganizations = $query->get();
        return $topOrganizations->toArray();
    }

    private function organizationOrderStatisticsMoreInfo(Collection $data)
    {
        $addons = Addon::pluck('name')->toArray();
        $addons = "('" . implode("','", $addons) . "')";

        $query = $this->baseQuery();
        $query->leftJoin('organizations', 'schools.organization_id', '=', 'organizations.id')
            ->select([
                'organizations.name',
                DB::raw('organizations.id as orgId'),
                DB::raw('courses.vehicle_segment_id as segmentId'),
                DB::raw('vehicle_segments.name as segment_name'),
                DB::raw('COUNT(IF(order_items.course_id IS NOT NULL && order_items.cancelled = false, orders.id, NULL)) as order_count'),
                DB::raw('COUNT(IF(order_items.course_id IS NOT NULL && order_items.cancelled = true, orders.id, NULL)) as cancelled_order_count'),
                DB::raw('SUM(case when order_items.cancelled = false and order_items.credited = false then order_items.amount * order_items.quantity else 0 end) as turnover'),

                DB::raw("SUM(case when (order_items.custom_addon_id IS NOT NULL OR order_items.type IN $addons) 
                    and (order_items.cancelled = false and order_items.credited = false) then order_items.amount * order_items.quantity else 0 end) as paket_turnover"),

                DB::raw("SUM(IF(order_items.custom_addon_id IS NOT NULL && order_items.cancelled = false, 1, 0))
                 + COUNT(IF(order_items.type IN $addons && order_items.cancelled = false, orders.id, NULL)) as paket_count"),
                DB::raw("SUM(IF(order_items.custom_addon_id IS NOT NULL && order_items.cancelled = true, 1, 0))
                 + COUNT(IF(order_items.type IN $addons && order_items.cancelled = true, orders.id, NULL)) as cancelled_paket_count"),
            ])
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('courses','order_items.course_id', '=', 'courses.id')
            ->leftJoin('vehicle_segments', 'vehicle_segments.id', '=', 'courses.vehicle_segment_id')
            ->groupBy(['organizations.id', 'courses.vehicle_segment_id']);

        return $query->get()->map(function ($item) {
            $item->segment_name = __('vehicle_segments.' . strtolower($item->segment_name));

            return $item;
        })->groupBy(['name'])->toArray();

    }

    /**
     * @param Collection $data
     * @return mixed
     */
    private function cityOrderStatistics(Collection $data)
    {//todo add addons count in query
        $query = $this->baseQuery();
        $query->join('cities', 'schools.city_id', '=', 'cities.id')
            ->groupBy('cities.id')
            ->select([
                'cities.name',
                DB::raw('cities.id as cityId'),
                DB::raw('SUM(case when order_items.course_id is not null then 1 else 0 end) as booked'),
                DB::raw('SUM(case when order_items.course_id is not null and (order_items.cancelled = 1 or order_items.credited = 1) then 1 else 0 end) as cancelled'),
                DB::raw('SUM(case when order_items.cancelled = 0 and order_items.credited = 0 then order_items.amount * order_items.quantity else 0 end) as turnover')
            ])
            ->orderBy('booked', 'desc');
        $topCities = $query->get();

        return $topCities->toArray();
    }

    private function cityOrderStatisticsMoreInfo(Collection $data)
    {
        $addons = Addon::pluck('name')->toArray();
        $addons = "('" . implode("','", $addons) . "')";

        $query = $this->baseQuery();
        $query->leftJoin('cities', 'schools.city_id', '=', 'cities.id')
            ->select([
            'cities.name',
            DB::raw('cities.id as cityId'),
            DB::raw('courses.vehicle_segment_id as segmentId'),
            DB::raw('vehicle_segments.name as segment_name'),
            DB::raw('COUNT(IF(order_items.course_id IS NOT NULL && order_items.cancelled = false, orders.id, NULL)) as order_count'),
            DB::raw('COUNT(IF(order_items.course_id IS NOT NULL && order_items.cancelled = true, orders.id, NULL)) as cancelled_order_count'),
            DB::raw('SUM(case when order_items.cancelled = false and order_items.credited = false then order_items.amount * order_items.quantity else 0 end) as turnover'),

            DB::raw("SUM(case when (order_items.custom_addon_id IS NOT NULL OR order_items.type IN $addons) 
                    and (order_items.cancelled = false and order_items.credited = false) then order_items.amount * order_items.quantity else 0 end) as paket_turnover"),

            DB::raw("SUM(IF(order_items.custom_addon_id IS NOT NULL && order_items.cancelled = false, 1, 0))
                 + COUNT(IF(order_items.type IN $addons && order_items.cancelled = false, orders.id, NULL)) as paket_count"),
            DB::raw("SUM(IF(order_items.custom_addon_id IS NOT NULL && order_items.cancelled = true, 1, 0))
                 + COUNT(IF(order_items.type IN $addons && order_items.cancelled = true, orders.id, NULL)) as cancelled_paket_count"),
        ])
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('courses','order_items.course_id', '=', 'courses.id')
            ->leftJoin('vehicle_segments', 'vehicle_segments.id', '=', 'courses.vehicle_segment_id')
            ->groupBy(['cities.id', 'courses.vehicle_segment_id']);

        return $query->get()->map(function ($item) {
            $item->segment_name = __('vehicle_segments.' . strtolower($item->segment_name));

            return $item;
        })->groupBy(['name'])->toArray();

    }



    /**
     * @param Collection $data
     * @return array
     */
    private function chartData(Collection $data)
    {
        DB::enableQueryLog();
        $query = $this->baseQuery()
            ->select(
                'order_items.id',
                'order_items.created_at',
                DB::raw('case when order_items.course_id then 1 else 0 end as booked'),
                DB::raw('case when order_items.course_id is not null and (order_items.cancelled = 1 or order_items.credited = 1) then 1 else 0 end as cancelled'),
                DB::raw('(order_items.amount * order_items.quantity) as turnover')
            );

        if ($data->get('cityId') > 0) {
            $query->where('schools.city_id', $data->get('cityId'));
        }

        if ($data->get('schoolId') > 0) {
            $query->where('schools.id', $data->get('schoolId'));
        }

        if ($data->get('organizationId') > 0) {
            $query->where('schools.organization_id', $data->get('organizationId'));
        }

        $chartData = $query->get();

        $chartData = $chartData->groupBy(function ($item, $key) use (&$dates) {
                $date = Carbon::createFromFormat('Y-m-d H:i:s', $item->created_at);
                return $date->format($this->granularityGroupFormat);
            })
            ->sortBy(function ($group) {
                return $group->first()->created_at;
            })
            ->map(function ($group) {
                $date = Carbon::createFromFormat('Y-m-d H:i:s', $group->first()->created_at);
                $dateStr = $date->format($this->granularityLabelFormat);
                return (object)[
                    'label' => $this->granularity === 'monthly' ? substr($dateStr, 0, 3) : $dateStr,
                    'booked' => $group->sum('booked'),
                    'cancelled' => $group->sum('cancelled'),
                    'turnover' => $group->sum('turnover')
                ];
            });

        return [
            'labels' => $chartData->map(function($group){ return $group->label; })->flatten(),
            'booked' => $chartData->map(function($group){ return $group->booked; })->flatten(),
            'last' => $chartData->map(function($group){ return $group->booked; })->flatten(),
            'cancelled' => $chartData->map(function($group){ return $group->cancelled; })->flatten(),
            'turnover' => $chartData->map(function($group){ return $group->turnover; })->flatten()
        ];
    }

    /**
     * @return mixed
     */
    private function baseQuery()
    {
        return DB::query()->from('order_items')
            ->whereBetween(\DB::raw('DATE(order_items.created_at)'), [$this->startDate->toDateString(), $this->endDate->toDateString()])
            ->join('schools', 'order_items.school_id', '=', 'schools.id');
    }

    /**
     * @param Carbon $startDate
     * @param Carbon $endDate
     */
    private function setDates(Carbon $startDate, Carbon $endDate)
    {
        switch ($this->granularity) {
            case 'yearly':
                $this->startDate = $startDate->startOfYear();
                $this->endDate = $endDate->endOfYear();
                $this->granularityGroupFormat = 'Y';
                $this->granularityLabelFormat = 'Y';
                break;
            case 'monthly':
                $this->startDate = $startDate->startOfMonth();
                $this->endDate = $endDate->endOfMonth();
                $this->granularityGroupFormat = 'Y-m';
                $this->granularityLabelFormat = 'F';
                break;
            case 'weekly':
                $this->startDate = $startDate->startOfWeek();
                $this->endDate = $endDate->endOfWeek();
                $this->granularityGroupFormat = 'Y-W';
                $this->granularityLabelFormat = 'W';
                break;
            default: // for daily
                $this->startDate = $startDate->startOfDay();
                $this->endDate = $endDate->endOfDay();
                $this->granularityGroupFormat = 'Y-m-d';
                $this->granularityLabelFormat = 'Y-m-d';
                break;
        }
    }
}
