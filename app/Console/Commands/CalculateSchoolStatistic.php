<?php

namespace Jakten\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Jakten\Models\School;

class CalculateSchoolStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:statistics {id=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate School Statistic';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ordersCancelled = DB::table('schools')
            ->select('schools.id', DB::raw('COUNT( orders.id ) as quantity'))
            ->join('orders', 'schools.id', '=', 'orders.school_id')
            ->where('orders.cancelled', '=', true)
            ->groupBy(['schools.id'])
            ->get();

        $ordersCancelledArray = [];
        foreach ($ordersCancelled as $ordersCancelledVal) {
            $ordersCancelledArray[(int)$ordersCancelledVal->id] = $ordersCancelledVal;
        }

        $ordersAll = DB::table('schools')
            ->select('schools.id', DB::raw('COUNT( orders.id ) as quantity'))
            ->join('orders', 'schools.id', '=', 'orders.school_id')
            ->groupBy(['schools.id'])
            ->get();

        $ordersAllArray = [];
        foreach ($ordersAll as $ordersAllVal) {
            $ordersAllArray[(int)$ordersAllVal->id] = $ordersAllVal;
        }

        $coursesInfo = DB::table('schools')
            ->select('schools.id', DB::raw('COUNT( courses.id ) as quantity'), DB::raw('SUM( courses.seats ) as sum_seats'))
            ->leftJoin('courses', 'schools.id', '=', 'courses.school_id')
            ->where('courses.start_time', '>', Carbon::now())
            ->where('courses.seats', '>', 0)
            ->where('courses.deleted_at', '=', null)
            ->groupBy(['schools.id'])
            ->get();

        $schoolRating = DB::table('schools')
            ->select('schools.id', DB::raw('AVG( school_ratings.rating ) as rating'))
            ->leftJoin('school_ratings', 'schools.id', '=', 'school_ratings.school_id')
            ->groupBy(['schools.id'])
            ->get();

        $ratingsAllArray = [];
        foreach ($schoolRating as $ratingsAllVal) {
            $ratingsAllArray[(int)$ratingsAllVal->id] = $ratingsAllVal;
        }

        $data = [];
        foreach ($coursesInfo as $val) {
            $all = isset($ordersCancelledArray[(int)$val->id]) ? (int)$ordersCancelledArray[(int)$val->id]->quantity ?: 1 : 0;
            $cancelled = isset($ordersAllArray[(int)$val->id]) ? (int)$ordersAllArray[(int)$val->id]->quantity ?: 1 : 0;
            $rating = isset($ratingsAllArray[(int)$val->id]) ? (int)$ratingsAllArray[(int)$val->id]->rating ?: 0 : 0;

            $data[] = [
                'school_id' => (int)$val->id,
                'courses' => (int)$val->quantity,
                'sum_seats' => (int)$val->sum_seats,
                'canceled' => isset($ordersCancelledArray[(int)$val->id]) ? (int)$ordersCancelledArray[(int)$val->id]->quantity : 0,
                'conversion' => (int)($cancelled/((int)($all/100) > 1 ? (int)($all/100) : 100)),
                'rating' => $rating,
                'avg_price' => 0,
            ];
        }

        $this->insertOrUpdate($data);
        Cache::forget('school_algorithm_info');
    }

    /**
     * Mass (bulk) insert or update on duplicate for Laravel 5
     *
     * insertOrUpdate([
     *   ['id'=>1,'value'=>10],
     *   ['id'=>2,'value'=>60]
     * ]);
     *
     *
     * @param array $rows
     * @return mixed
     */
    function insertOrUpdate(array $rows){
        $table = 'school_algorithm_info';

        $first = reset($rows);

        $columns = implode( ',',
            array_map( function( $value ) { return "$value"; } , array_keys($first) )
        );

        $values = implode( ',', array_map( function( $row ) {
                return '('.implode( ',',
                        array_map( function( $value ) { return '"'.str_replace('"', '""', $value).'"'; } , $row )
                    ).')';
            } , $rows )
        );

        $updates = implode( ',',
            array_map( function( $value ) { return "$value = VALUES($value)"; } , array_keys($first) )
        );

        $sql = "INSERT INTO {$table}({$columns}) VALUES {$values} ON DUPLICATE KEY UPDATE {$updates}";

        return DB::statement( $sql );
    }
}
