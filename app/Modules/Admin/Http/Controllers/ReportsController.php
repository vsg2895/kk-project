<?php namespace Admin\Http\Controllers;

use Barryvdh\DomPDF\Facade as PDF;
use Jakten\Exports\StudentsExport;
use DB;
use Jakten\Facades\Auth;
use Jakten\Models\School;
use Jakten\Models\VehicleSegment;
use Jakten\Repositories\CourseParticipantsRepository;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\LoyaltyProgramService;
use Maatwebsite\Excel\Facades\Excel;
use Shared\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory as View;
use Admin\Http\Requests\SearchOrdersRequest;

/**
 * Class ReportsController
 * @package Admin\Http\Controllers
 */
class ReportsController extends Controller
{
    /**
     * @var CourseParticipantsRepository
     */
    private $repository;
    /**
     * @var LoyaltyProgramService
     */
    private $loyaltyProgramService;

    public function __construct(
        CourseParticipantsRepository $repository,
        KKJTelegramBotService $botService,
        LoyaltyProgramService $loyaltyProgramService
    )
    {
        parent::__construct($botService);
        $this->repository = $repository;
        $this->loyaltyProgramService = $loyaltyProgramService;
    }

    /**
     * @return View
     */
    public function index()
    {
        return view('admin::reports.index');
    }

    /**
     * @param SearchOrdersRequest $request
     * @return View
     */
    public function orders(SearchOrdersRequest $request)
    {
        $schools = DB::table('schools')->where('deleted_at', '=', null)->get();
        $loyaltyVegicleFeeIds = implode(',', VehicleSegment::LOYALTY_DISCOUNT_LIST);
        $currentYear = "'" . $this->loyaltyProgramService->getCurrentYearDate() . "'";
        $prevYear = "'" . $this->loyaltyProgramService->getPreviousYearDate() . "'";

        $result = DB::select(DB::raw("SELECT o.id as order_id, o.created_at as booking_date, c.start_time as course_date, 
            CONCAT(cp.given_name, ' ', cp.family_name) as user_name, oi.type, (oi.amount * oi.quantity) as amount, o.payment_method,
            CONCAT(u.given_name, ' ', u.family_name) as buyer, 
            IF(ssp.fee IS NOT NULL, ssp.fee, IF(sa.fee IS NOT NULL, sa.fee, IF(ca.fee IS NOT NULL, ca.fee, IF(oi.course_id IS NOT NULL, 
            IF(vf.fee IS NOT NULL, vf.fee , " . config('fees.courses') . "), " . config('fees.packages') . "))))
            - IF(vf.id IN (".$loyaltyVegicleFeeIds."), loyalty_query.fee, 0) 
            + IF(vf.id IN (".$loyaltyVegicleFeeIds.") AND (SELECT top_partner FROM schools WHERE id = oi.school_id), 2.5, 0) as provision
            FROM
                (SELECT 
                    IF(prevYear.total_amount >= 750000, IF(thisYear.total_amount + 25000 >= 750000, 2, IF(thisYear.total_amount + 25000 >= 500000, 1.5, IF(thisYear.total_amount + 25000 >= 250000, 1, 0))), 
                    IF(thisYear.total_amount >= 750000, 2, IF(thisYear.total_amount >= 500000, 1.5, IF(thisYear.total_amount >= 250000, 1, 0))))
                    as fee, thisYear.school_id
                FROM
                    (SELECT (SUM(oit.amount * oit.quantity) + schools.loyalty_fixed_amount) AS total_amount, school_id
                FROM `order_items` as oit 
                        LEFT JOIN schools ON schools.id = oit.school_id
                        WHERE DATE(oit.created_at) >= ".$currentYear."
                        AND oit.cancelled = false
                        AND EXISTS (SELECT * FROM orders WHERE oit.order_id = orders.id AND orders.cancelled = false)
                        GROUP BY oit.school_id) as thisYear
                LEFT JOIN
                    (SELECT 
                        SUM(oit.amount * oit.quantity) AS total_amount, 
                        school_id
                    FROM `order_items` as oit
                        WHERE DATE(oit.created_at) >= ".$prevYear." AND DATE(oit.created_at) < ".$currentYear."
                        AND oit.cancelled = false
                        AND EXISTS (SELECT * FROM orders WHERE oit.order_id = orders.id AND orders.cancelled = false)
                        GROUP BY oit.school_id) as prevYear ON prevYear.school_id = thisYear.school_id) as loyalty_query
            LEFT JOIN
            
            order_items as oi ON loyalty_query.school_id = oi.school_id
              LEFT JOIN course_participants as cp ON cp.order_item_id = oi.id
              LEFT JOIN orders as o ON o.id = oi.order_id
              LEFT JOIN courses as c ON c.id = oi.course_id
              LEFT JOIN users as u ON u.id = o.user_id

             LEFT JOIN vehicle_fee as vf ON vf.id = c.vehicle_segment_id
             LEFT JOIN school_segment_prices as ssp ON ssp.school_id = oi.school_id and c.vehicle_segment_id =  ssp.vehicle_segment_id
             LEFT JOIN schools_addons as sa ON sa.school_id = oi.school_id and oi.custom_addon_id =  sa.addon_id
             LEFT JOIN custom_addons as ca ON ca.school_id = oi.school_id and oi.custom_addon_id =  ca.id

            WHERE ((oi.course_id IS NULL AND 
                (o.school_id = :school AND oi.created_at >= :start_time_copy AND oi.created_at <=:end_time_copy) AND oi.amount > 0)
              OR (oi.course_id IS NOT NULL AND
                (o.school_id = :school_copy AND c.start_time >= :start_time AND c.start_time <= :end_time))) 
              AND o.cancelled = :cancelled AND oi.is_kkj_klarna = :is_kkj_klarna   AND oi.type != :type AND oi.amount > 0
            ORDER BY c.start_time"), [
                'school' => $request->get('school'),
                'school_copy' => $request->get('school'),
                'cancelled' => false,
                'is_kkj_klarna' => true,
                'start_time' => $request->get('start_time'),
                'start_time_copy' => $request->get('start_time'),
                'end_time' =>$request->get('end_time'),
                'type' => config('klarna.promo_cart_name'),
                'end_time_copy' =>$request->get('end_time')
            ]
        );
        $groups = collect($result)->groupBy('order_id');

        $orders = collect();

        foreach ($groups as $group) {
            $order = $group->first();
            $order->total_sum_of_order = $group->sum('amount');
            $order->user_names = $group->pluck('user_name')->all();
            $orders->push($order);
        }

        return view('admin::reports.orders', compact('schools', 'orders', 'groups'));
    }

    /**
     * @param SearchOrdersRequest $request
     * @return View
     */
    public function schools(SearchOrdersRequest $request)
    {
        $loyaltyVegicleFeeIds = implode(',', VehicleSegment::LOYALTY_DISCOUNT_LIST);
        $currentYear = "'" . $this->loyaltyProgramService->getCurrentYearDate() . "'";
        $prevYear = "'" . $this->loyaltyProgramService->getPreviousYearDate() . "'";

        $result = DB::select(DB::raw("SELECT
                s.id, s.name, SUM(oi.amount * oi.quantity) as total, 
                SUM((oi.amount * oi.quantity) - ((oi.amount* oi.quantity)*
                ((IF(ssp.fee IS NOT NULL, ssp.fee, IF(sa.fee IS NOT NULL, sa.fee, IF(ca.fee IS NOT NULL, ca.fee, IF(oi.course_id IS NOT NULL, IF(vf.fee IS NOT NULL, vf.fee , " . config('fees.courses') . "), " . config('fees.packages')  . "))))
                 - IF(vf.id IN (".$loyaltyVegicleFeeIds."), loyalty_query.fee, 0) 
                 + IF(vf.id IN (".$loyaltyVegicleFeeIds.") AND (SELECT top_partner FROM schools WHERE id = oi.school_id), 2.5, 0)) /100)))
                as school_commission, 
                COUNT(DISTINCT o.id) * " . config('fees.booking_fee_to_kkj') . " as booking_fee
            FROM
                (SELECT 
                    IF(prevYear.total_amount >= 750000, IF(thisYear.total_amount + 25000 >= 750000, 2, IF(thisYear.total_amount + 25000 >= 500000, 1.5, IF(thisYear.total_amount + 25000 >= 250000, 1, 0))), 
                    IF(thisYear.total_amount >= 750000, 2, IF(thisYear.total_amount >= 500000, 1.5, IF(thisYear.total_amount >= 250000, 1, 0)))) 
                    as fee, thisYear.school_id
                FROM
                    (SELECT (SUM(oit.amount * oit.quantity) + schools.loyalty_fixed_amount) AS total_amount, school_id
                FROM `order_items` as oit 
                        LEFT JOIN schools ON schools.id = oit.school_id
                        WHERE DATE(oit.created_at) >= ".$currentYear."
                        AND oit.cancelled = false
                        AND EXISTS (SELECT * FROM orders WHERE oit.order_id = orders.id AND orders.cancelled = false)
                        GROUP BY oit.school_id) as thisYear
                LEFT JOIN
                    (SELECT 
                        SUM(oit.amount * oit.quantity) AS total_amount, 
                        school_id
                    FROM `order_items` as oit
                        WHERE DATE(oit.created_at) >= ".$prevYear." AND DATE(oit.created_at) < ".$currentYear."
                        AND oit.cancelled = false
                        AND EXISTS (SELECT * FROM orders WHERE oit.order_id = orders.id AND orders.cancelled = false)
                        GROUP BY oit.school_id) as prevYear ON prevYear.school_id = thisYear.school_id) as loyalty_query
            LEFT JOIN
            
            order_items as oi ON loyalty_query.school_id = oi.school_id
                 JOIN orders as o ON o.id = oi.order_id
                 LEFT JOIN courses as c ON c.id = oi.course_id
                 JOIN schools as s ON s.id = oi.school_id

                 LEFT JOIN vehicle_fee as vf ON vf.id = c.vehicle_segment_id
                 LEFT JOIN school_segment_prices as ssp ON ssp.school_id = oi.school_id and c.vehicle_segment_id =  ssp.vehicle_segment_id
                 LEFT JOIN schools_addons as sa ON sa.school_id = oi.school_id and oi.custom_addon_id =  sa.addon_id
                 LEFT JOIN custom_addons as ca ON ca.school_id = oi.school_id and oi.custom_addon_id =  ca.id

            WHERE ((oi.course_id IS NULL AND 
                (oi.created_at >= :start_time_copy AND oi.created_at <=:end_time_copy) AND oi.amount > 0)
              OR (oi.course_id IS NOT NULL AND
                (c.start_time >= :start_time AND c.start_time <= :end_time)))
                AND oi.is_kkj_klarna = :is_kkj_klarna AND o.cancelled = :cancelled   AND oi.type != :type AND oi.amount > 0
                GROUP BY s.id ORDER BY s.name"), [
                'is_kkj_klarna' => true,
                'start_time' => $request->get('start_time'),
                'cancelled' => false,
                'start_time_copy' => $request->get('start_time'),
                'end_time' =>$request->get('end_time'),
                'type' => config('klarna.promo_cart_name'),
                'end_time_copy' =>$request->get('end_time')
            ]
        );

        $schools = collect($result);

        return view('admin::reports.schools', compact('schools'));
    }

    /**
     * @return View|\Illuminate\View\View
     */
    public function students()
    {
        $students = $this->repository->students()->get();

        return view('admin::reports.students', compact('students'));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function studentsExport()
    {
        return Excel::download(new StudentsExport($this->repository->students()), time() . 'students.xlsx');
    }

    /**
     * @param SearchOrdersRequest $request
     */
    public function downloadOrders(SearchOrdersRequest $request)
    {
        $loyaltyVegicleFeeIds = implode(',', VehicleSegment::LOYALTY_DISCOUNT_LIST);
        $currentYear = "'" . $this->loyaltyProgramService->getCurrentYearDate() . "'";
        $prevYear = "'" . $this->loyaltyProgramService->getPreviousYearDate() . "'";

        $results = DB::select(DB::raw("SELECT o.id as order_id, o.created_at as booking_date, c.start_time as course_date,
            IF (cp.id , CONCAT(cp.given_name, ' ', cp.family_name), CONCAT(ap.given_name, ' ', ap.family_name)) as user_name, oi.type, (oi.amount * oi.quantity) as amount, o.payment_method,
            CONCAT(u.given_name, ' ', u.family_name) as buyer,
            IF(ssp.fee IS NOT NULL, ssp.fee, IF(sa.fee IS NOT NULL, sa.fee, IF(ca.fee IS NOT NULL, ca.fee, IF(oi.course_id IS NOT NULL, IF(vf.fee IS NOT NULL, vf.fee , " . config('fees.courses') . "), " . config('fees.packages') . ")))) 
            - IF(vf.id IN (".$loyaltyVegicleFeeIds."), loyalty_query.fee, 0) 
            + IF(vf.id IN (".$loyaltyVegicleFeeIds.") AND (SELECT top_partner FROM schools WHERE id = oi.school_id), 2.5, 0) as provision
            FROM
                (SELECT 
                    IF(prevYear.total_amount >= 750000, IF(thisYear.total_amount + 25000 >= 750000, 2, IF(thisYear.total_amount + 25000 >= 500000, 1.5, IF(thisYear.total_amount + 25000 >= 250000, 1, 0))), 
                    IF(thisYear.total_amount >= 750000, 2, IF(thisYear.total_amount >= 500000, 1.5, IF(thisYear.total_amount >= 250000, 1, 0)))) 
                    as fee, thisYear.school_id
                FROM
                    (SELECT (SUM(oit.amount * oit.quantity) + schools.loyalty_fixed_amount) AS total_amount, school_id
                FROM `order_items` as oit 
                        LEFT JOIN schools ON schools.id = oit.school_id
                        WHERE DATE(oit.created_at) >= ".$currentYear."
                        AND oit.cancelled = false
                        AND EXISTS (SELECT * FROM orders WHERE oit.order_id = orders.id AND orders.cancelled = false)
                        GROUP BY oit.school_id) as thisYear
                LEFT JOIN
                    (SELECT 
                        SUM(oit.amount * oit.quantity) AS total_amount, 
                        school_id
                    FROM `order_items` as oit
                        WHERE DATE(oit.created_at) >= ".$prevYear." AND DATE(oit.created_at) < ".$currentYear."
                        AND oit.cancelled = false
                        AND EXISTS (SELECT * FROM orders WHERE oit.order_id = orders.id AND orders.cancelled = false)
                        GROUP BY oit.school_id) as prevYear ON prevYear.school_id = thisYear.school_id) as loyalty_query
            LEFT JOIN
            
             order_items as oi ON loyalty_query.school_id = oi.school_id
              LEFT JOIN addon_participants as ap ON ap.order_item_id = oi.id
              LEFT JOIN course_participants  as cp ON cp.order_item_id = oi.id
              LEFT JOIN orders as o ON o.id = oi.order_id
              LEFT JOIN courses as c ON c.id = oi.course_id
              LEFT JOIN users as u ON u.id = o.user_id

             LEFT JOIN vehicle_fee as vf ON vf.id = c.vehicle_segment_id
             LEFT JOIN school_segment_prices as ssp ON ssp.school_id = oi.school_id and c.vehicle_segment_id =  ssp.vehicle_segment_id
             LEFT JOIN schools_addons as sa ON sa.school_id = oi.school_id and oi.custom_addon_id =  sa.addon_id
             LEFT JOIN custom_addons as ca ON ca.school_id = oi.school_id and oi.custom_addon_id =  ca.id

            WHERE ((oi.course_id IS NULL AND 
                (o.school_id = :school AND oi.created_at >= :start_time_copy AND oi.created_at <=:end_time_copy))
              OR (oi.course_id IS NOT NULL AND
                (o.school_id = :school_copy AND c.start_time >= :start_time AND c.start_time <= :end_time))) 
              AND o.cancelled = :cancelled AND oi.is_kkj_klarna = :is_kkj_klarna AND oi.type != :type AND oi.amount > 0
            ORDER BY c.start_time"), [
                'school' => $request->get('school'),
                'school_copy' => $request->get('school'),
                'cancelled' => false,
                'is_kkj_klarna' => true,
                'start_time' => $request->get('start_time'),
                'start_time_copy' => $request->get('start_time'),
                'end_time' =>$request->get('end_time'),
                'type' => config('klarna.promo_cart_name'),
                'end_time_copy' =>$request->get('end_time')
            ]
        );

        $groups = collect($results)->groupBy('order_id');

//        dd($groups);

        $orders = collect();
        foreach ($groups as $group) {
            $order = $group->first();
            $order->total_sum_of_order = $group->sum('amount');
            $order->type_names = $group->pluck('type')->all();
            $order->user_names = $group->pluck('user_name')->all();
            $orders->push($order);
        }

        $data = [
            'total' => 0,
        ];

        foreach ($results as $result) {
            $schoolPayout = $result->amount - ($result->amount * ($result->provision/100));

            if (!isset($data['types'][$result->type])) {
                $data['types'][$result->type]['val'] = 0;
                $data['types'][$result->type]['prov'] = $result->provision;
            }

            $data['types'][$result->type]['val'] += $result->amount * ($result->provision/100);
            $data['total'] += $schoolPayout;

        }

        $data['netto'] = $data['total'] * 0.75;
        $data['vat'] = $data['total'] * 0.25;
        $pdf = PDF::loadView('admin::reports.download-orders', ['orders' => $orders, 'orderItems' => $groups, 'data' => $data]);

        $result = DB::table('invoice')
            ->select('id')
            ->latest('id')
            ->first();

        $school = School::findOrFail($request->get('school'));

        return $pdf->stream($result->id . '_' . $school->name .'_underlag.pdf');
    }

    public function downloadInvoice(SearchOrdersRequest $request)
    {
        $school = School::findOrFail($request->get('school'));
        $loyaltyVegicleFeeIds = implode(',', VehicleSegment::LOYALTY_DISCOUNT_LIST);
        $currentYear = "'" . $this->loyaltyProgramService->getCurrentYearDate() . "'";
        $prevYear = "'" . $this->loyaltyProgramService->getPreviousYearDate() . "'";

        $results = DB::select(DB::raw("SELECT o.id as order_id, o.created_at as booking_date, c.start_time as course_date, 
            CONCAT(cp.given_name, ' ', cp.family_name) as user_name, oi.type, (oi.amount * oi.quantity) as amount, o.payment_method,
            CONCAT(u.given_name, ' ', u.family_name) as buyer, IF(a.name IS NOT NULL, a.name, ca.name) as addon_name,
            IF(ssp.fee IS NOT NULL, ssp.fee, IF(sa.fee IS NOT NULL, sa.fee, IF(ca.fee IS NOT NULL, ca.fee, IF(oi.course_id IS NOT NULL, IF(vf.fee IS NOT NULL, vf.fee , " . config('fees.courses') . "), " . config('fees.packages') . "))))
             - IF(vf.id IN (".$loyaltyVegicleFeeIds."), loyalty_query.fee, 0)
              + IF(vf.id IN (".$loyaltyVegicleFeeIds.") AND (SELECT top_partner FROM schools WHERE id = oi.school_id), 2.5, 0) as provision,
             oi.course_id
            FROM
                (SELECT 
                    IF(prevYear.total_amount >= 750000, IF(thisYear.total_amount + 25000 >= 750000, 2, IF(thisYear.total_amount + 25000 >= 500000, 1.5, IF(thisYear.total_amount + 25000 >= 250000, 1, 0))), 
                    IF(thisYear.total_amount >= 750000, 2, IF(thisYear.total_amount >= 500000, 1.5, IF(thisYear.total_amount >= 250000, 1, 0)))) 
                    as fee, thisYear.school_id
                FROM
                    (SELECT (SUM(oit.amount * oit.quantity) + schools.loyalty_fixed_amount) AS total_amount, school_id
                FROM `order_items` as oit 
                        LEFT JOIN schools ON schools.id = oit.school_id
                        WHERE DATE(oit.created_at) >= ".$currentYear."
                        AND oit.cancelled = false
                        AND EXISTS (SELECT * FROM orders WHERE oit.order_id = orders.id AND orders.cancelled = false)
                        GROUP BY oit.school_id) as thisYear
                LEFT JOIN
                    (SELECT 
                        SUM(oit.amount * oit.quantity) AS total_amount, 
                        school_id
                    FROM `order_items` as oit
                        WHERE DATE(oit.created_at) >= ".$prevYear." AND DATE(oit.created_at) < ".$currentYear."
                        AND oit.cancelled = false
                        AND EXISTS (SELECT * FROM orders WHERE oit.order_id = orders.id AND orders.cancelled = false)
                        GROUP BY oit.school_id) as prevYear ON prevYear.school_id = thisYear.school_id) as loyalty_query
            LEFT JOIN
            order_items as oi ON loyalty_query.school_id = oi.school_id
              LEFT JOIN course_participants as cp ON cp.order_item_id = oi.id
              LEFT JOIN orders as o ON o.id = oi.order_id
              LEFT JOIN courses as c ON c.id = oi.course_id
              LEFT JOIN users as u ON u.id = o.user_id

             LEFT JOIN vehicle_fee as vf ON vf.id = c.vehicle_segment_id
             LEFT JOIN school_segment_prices as ssp ON ssp.school_id = oi.school_id and c.vehicle_segment_id =  ssp.vehicle_segment_id
             LEFT JOIN schools_addons as sa ON sa.school_id = oi.school_id and oi.custom_addon_id =  sa.addon_id
             LEFT JOIN addons as a ON sa.addon_id = a.id
             LEFT JOIN custom_addons as ca ON ca.school_id = oi.school_id and oi.custom_addon_id =  ca.id

            WHERE ((oi.course_id IS NULL AND 
                (o.school_id = :school AND oi.created_at >= :start_time_copy AND oi.created_at <=:end_time_copy))
              OR (oi.course_id IS NOT NULL AND
                (o.school_id = :school_copy AND c.start_time >= :start_time AND c.start_time <= :end_time))) 
              AND o.cancelled = :cancelled AND oi.is_kkj_klarna = :is_kkj_klarna   AND oi.type != :type AND oi.amount > 0
            ORDER BY o.id"), [
                'school' => $request->get('school'),
                'school_copy' => $request->get('school'),
                'cancelled' => false,
                'is_kkj_klarna' => true,
                'start_time' => $request->get('start_time'),
                'start_time_copy' => $request->get('start_time'),
                'end_time' =>$request->get('end_time'),
                'type' => config('klarna.promo_cart_name'),
                'end_time_copy' =>$request->get('end_time')
            ]
        );

        $courses = [
            'total' => 0,
            'amount' => 0
        ];
        $packages = [
            'total' => 0,
            'amount' => 0
        ];

        $data = [
            'total' => 0,
            'netto' => 0,
            'vat' => 0,
            'total_moms_edu' => 0,
            'moms_edu' => 0,
            'moms' => 0,
            'total_moms' => 0,
            'school' => $school,
            'schoolsTotal' => 0,
            'klarnaCommision' => 0,
            'total_edu' => 0,
            'total_not_edu' => 0,
        ];

        foreach ($results as $result) {
            $schoolPayout = $result->amount - ($result->amount * ((($result->provision/100) + (config('fees.klarna')/100))));
            $data['schoolsTotal'] += $result->amount;

            $data['total'] += $schoolPayout;

            if (!isset($data['types'][$result->type])) {
                $data['types'][$result->type] = 0;
            }

            $data['types'][$result->type] += $schoolPayout;

            $moms = (config('fees.moms_inv'))/100;

            if ($result->course_id) {
                $courses['total'] += $schoolPayout;
                $courses['amount'] += 1;
                $data['moms'] += $schoolPayout * $moms;
                $data['total_moms'] += $schoolPayout;
                $data['total_not_edu'] += $result->amount;
            } else {
                if (str_contains($result->addon_name, 'boken')) {
                    $moms = (config('fees.moms_edu_inv'))/100;
                    $data['total_moms_edu'] += $schoolPayout;
                    $data['total_edu'] += $result->amount;
                    $data['moms_edu'] += $schoolPayout * $moms;
                } else {
                    $data['total_moms'] += $schoolPayout;
                    $data['total_not_edu'] += $result->amount;
                    $data['moms'] += $schoolPayout * $moms;
                }
                $packages['total'] += $schoolPayout;
                $packages['amount'] += 1;
            }

            $data['netto'] += $schoolPayout * (1 - $moms);
            $data['vat'] += $schoolPayout * $moms;
        }

        $data['klarnaCommision'] = ($data['schoolsTotal'] * (config('fees.klarna')/100));
        $data['courses'] = $courses;
        $data['packages'] = $packages;

        DB::table('invoice')->insert([
            'user_id' => Auth::user()->id,
        ]);

        $result = DB::table('invoice')
            ->select('id')
            ->latest('id')
            ->first();

        $data['invoice_id'] = $result->id;

        $pdf = PDF::loadView('admin::reports.download-invoice', $data);

        return $pdf->stream($result->id . '_' . $school->name .'_faktura.pdf');
    }
}
