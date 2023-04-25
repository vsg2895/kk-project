<?php namespace Shared\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jakten\Facades\Auth;
use Jakten\Services\OrderService;
use Jakten\Services\Payment\Klarna\KlarnaCheckoutOrder;
use Jakten\Models\{Order, PendingExternalOrder, School};
use Jakten\Repositories\Contracts\{CityRepositoryContract,
    SchoolRepositoryContract,
    CourseRepositoryContract,
    GiftCardTypeRepositoryContract,
    VehicleSegmentRepositoryContract};
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\Payment\Klarna\KlarnaService;
use Jakten\Services\Schema\CourseService as SchemaCourseService;

/**
 * Class PaymentController
 * @package Shared\Http\Controllers
 */
class PaymentController extends Controller
{
    /**
     * @constant RESULT_TYPE_COURSE
     */
    const RESULT_TYPE_COURSE = 'COURSE';

    /**
     * @var CourseRepositoryContract
     */
    private $courses;

    /**
     * @var KlarnaService
     */
    private $klarnaService;

    /**
     * @var SchemaCourseService
     */
    private $schemaCourseService;

    /**
     * @var CityRepositoryContract
     */
    private $cities;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var VehicleSegmentRepositoryContract
     */
    private $vehicleSegments;

    /**
     * @var GiftCardTypeRepositoryContract
     */
    private $giftCardTypes;

    /**
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * CourseController constructor.
     *
     * @param CourseRepositoryContract $courses
     * @param KlarnaService $klarnaService
     * @param SchemaCourseService $schemaCourseService
     * @param CityRepositoryContract $cities
     * @param Request $request
     * @param VehicleSegmentRepositoryContract $vehicleSegments
     * @param GiftCardTypeRepositoryContract $giftCardTypes
     * @param SchoolRepositoryContract $schools
     * @param KKJTelegramBotService $botService
     */
    public function __construct(
        CourseRepositoryContract $courses,
        KlarnaService $klarnaService,
        SchemaCourseService $schemaCourseService,
        CityRepositoryContract $cities,
        Request $request,
        VehicleSegmentRepositoryContract $vehicleSegments,
        GiftCardTypeRepositoryContract $giftCardTypes,
        SchoolRepositoryContract $schools,
        KKJTelegramBotService $botService
    )
    {
        parent::__construct($botService);
        $this->courses = $courses;
        $this->klarnaService = $klarnaService;
        $this->schemaCourseService = $schemaCourseService;
        $this->cities = $cities;
        $this->request = $request;
        $this->vehicleSegments = $vehicleSegments;
        $this->giftCardTypes = $giftCardTypes;
        $this->schools = $schools;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Klarna_Checkout_ApiErrorException
     */
    //TODO: Should be done on the post since we are creating stuff
    public function index(Request $request)
    {
        $schoolId = $request->query->get('skola');
        $courseIds = $request->query->get('kurser') ? explode(',', $request->query->get('kurser')) : null;
        $courses = $courseIds ? $this->courses->getCourses($courseIds, 'school.organization') : collect([]);
        $addonIds = $request->query->get('tillagg') ? explode(',', $request->query->get('tillagg')) : [];
        $customIds = $request->query->get('paket') ? explode(',', $request->query->get('paket')) : [];

        // check to make sure all courses are from the same school
        if ($courses && $courses->count()) {
            $sameSchool = $courses->map(function ($course) use ($courses) {
                return $courses->first()->school_id === $course->school_id;
            })->all();
            if (!(count(array_unique($sameSchool)) === 1 && end($sameSchool))) {
                abort(400);
            }
        }

        $school = School::whereId($schoolId)->first();

        $addons = $customAddons = [];

        if ($addonIds) {
            $addonsItems = $school->addons->whereIn('id', $addonIds);

            foreach($addonsItems as $item) {
                $addons[] = [
                    'local_id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->pivot->price,
                    'quantity' => 1,
                ];
            }
        }

        if ($customIds) {
            $customAddonsItems = $school->customAddons->whereIn('id', $customIds);

            foreach($customAddonsItems as $customItem) {
                    $customAddons[] = [
                        'local_id' => $customItem->id,
                        'name' => $customItem->name,
                        'price' => $customItem->price,
                        'quantity' => 1,
                    ];
            }
        }

        $klarnaOrder = null;

        if ($school) {
            $merchantId = env('KLARNA_V3_KKJ_PAYMENT_ID');
            $user = Auth::user();

            try {

                if ($request->session()->has('klarna_order_id') && $request->session()->get('merchant_id') == $merchantId) {
                    $klarnaOrderId = $request->session()->get('klarna_order_id');
                    $pendingOrderExists = PendingExternalOrder::where('external_order_id', $klarnaOrderId)->exists();
                    if ($pendingOrderExists) {
                        $klarnaOrder = $this->klarnaService->getOrder($school, $klarnaOrderId, true);
                    } else {
                        $klarnaOrder = $this->klarnaService->createOrder($school, $courses, $user, [1], [], $addons, $customAddons);
                    }
                } else {
                    $klarnaOrder = $this->klarnaService->createOrder($school, $courses, $user, [1], [], $addons, $customAddons);
                }

            } catch (\Exception $e) {
                $klarnaOrder = null;
            }

            $request->session()->put('klarna_order_id', 0);
            $request->session()->put('klarna_order_id', $klarnaOrder['id']);
            $request->session()->put('merchant_id', $merchantId);
        }
        $courseIds = $courses->map(function ($course) use ($courses) {
            return $course->id;
        })->toJson();

        $viewName = $request->query->get('iframe') === 'true' ? 'shared::iframe.payment.index' : 'shared::payment.index';

        return view($viewName, [
            'schoolId' => $schoolId,
            'courseIds' => $courseIds,
            'addonIds' => json_encode(array_map('intval', $addonIds)),
            'customIds' => json_encode(array_map('intval', $customIds)),
            'schemaCourse' => $this->schemaCourseService->getLdJsonTag(),
            'klarnaOrder' => $klarnaOrder,
        ]);
    }

    /**
     * @param null $schoolSlug
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function confirmed($schoolSlug = null, Request $request)
    {
        $klarnaOrderId = $request->input('klarna_order_id');
        $request->session()->forget('klarna_order_id');
        $request->session()->forget('merchant_id');
        $routeParams['schoolId'] = '';
        $routeParams['klarna_order_id'] = $klarnaOrderId;

        if ($schoolSlug) {
            $school = $this->schools->bySlug($schoolSlug)->with('city')->first();
            $citySlug = $school->city->slug;

            $klarnaOrder = null;
            if ($school) {
                $klarnaOrder = DB::transaction(function () use ($request, $school, $klarnaOrderId) {
                    $klarnaOrder = $this->klarnaService->getOrder($school, $klarnaOrderId, true);
                    if ($school) {
                        $routeParams['schoolId'] = $school->id;
                    }

                    if (config('app.env') === 'local') {
                        $klarnaOrder['merchant']['push_uri'] = route('public::klarna.checkout.push', $routeParams);
                    }

                    return $klarnaOrder;
                });
            }
        } else {
            $klarnaOrder = DB::transaction(function () use ($request, $klarnaOrderId) {
                $klarnaOrder = $this->klarnaService->getOrder(null, $klarnaOrderId, true);

                if (config('app.env') === 'local') {
                    $klarnaOrder['merchant']['push_uri'] = route('public::klarna.checkout.push', $routeParams);
                }

                return $klarnaOrder;
            });
        }

        $rebookingItem = [];
        foreach ($klarnaOrder['order_lines'] as $item) {
            if ($item['name'] == config('klarna.rebooking')) {
                $rebookingItem = $item;
            }
        }

        if (count($rebookingItem) && auth()->user()) {
            /** @var Order $order */
            $order = Order::query()->where('external_order_id', $klarnaOrderId)->first();

            if (!$order && (float)auth()->user()->amount > 0) {
                auth()->user()->amount -= abs($rebookingItem['total_amount'] / 100);
                auth()->user()->save();
            }
        }

        if ($schoolSlug) {
            return view('shared::payment.confirmed', compact('klarnaOrder', 'school', 'citySlug'));
        }

        return view('shared::payment.confirmed', compact('klarnaOrder'));
    }
}
