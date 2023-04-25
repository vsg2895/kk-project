<?php

namespace Shared\Http\Controllers;

use Carbon\Carbon;
use Complex\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jakten\{Helpers\Prices, Models\PendingExternalOrder};
use Jakten\Facades\Auth;
use Jakten\Presenters\SimplifiedCities;
use Jakten\Repositories\Contracts\{CityRepositoryContract,
    CourseRepositoryContract,
    SchoolRepositoryContract,
    VehicleSegmentRepositoryContract};
use Jakten\Services\{KKJTelegramBotService, Payment\Klarna\KlarnaService, Schema\CourseService as SchemaCourseService};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CourseController
 * @package Shared\Http\Controllers
 */
class CourseController extends Controller
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
     * @var SchoolRepositoryContract
     */
    private $schoolRepo;

    /**
     * CourseController constructor.
     *
     * @param CourseRepositoryContract $courses
     * @param KlarnaService $klarnaService
     * @param SchemaCourseService $schemaCourseService
     * @param CityRepositoryContract $cities
     * @param Request $request
     * @param VehicleSegmentRepositoryContract $vehicleSegments
     * @param KKJTelegramBotService $botService
     */
    public function __construct(CourseRepositoryContract $courses, KlarnaService $klarnaService,
                                SchemaCourseService $schemaCourseService, CityRepositoryContract $cities, Request $request, VehicleSegmentRepositoryContract $vehicleSegments, KKJTelegramBotService $botService,
                                SchoolRepositoryContract $schoolRepo)
    {
        parent::__construct($botService);
        $this->courses = $courses;
        $this->klarnaService = $klarnaService;
        $this->schemaCourseService = $schemaCourseService;
        $this->cities = $cities;
        $this->request = $request;
        $this->vehicleSegments = $vehicleSegments;
        $this->schoolRepo = $schoolRepo;
    }

    /**
     * Introduktionskurs
     *
     * @param SimplifiedCities $presenter
     * @param string $citySlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function intro(SimplifiedCities $presenter, $citySlug = null)
    {
        if ($citySlug) {
            return $this->index($citySlug, Prices::INTRODUCTION_CAR);
        }
        $cities = $presenter->format($this->cities->getForSelect('schools'));
        return view('shared::search.intro', compact('cities'));
    }

    /**
     * Riskettan
     *
     * @param SimplifiedCities $presenter
     * @param string $citySlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function riskettan(SimplifiedCities $presenter, $citySlug = null)
    {
        if ($citySlug) {
            return $this->index($citySlug, Prices::RISK_ONE_CAR);
        }
        $cities = $presenter->format($this->cities->getForSelect('schools'));
        return view('shared::search.riskettan', compact('cities'));
    }

    /**
     * Teorilektion
     *
     * @param SimplifiedCities $presenter
     * @param string $citySlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function teorilektion(SimplifiedCities $presenter, $citySlug = null)
    {
        if ($citySlug) {
            return $this->index($citySlug, Prices::THEORY_LESSON_CAR);
        }
        $cities = $presenter->format($this->cities->getForSelect('schools'));
        return view('shared::search.teorilektion', compact('cities'));
    }

    public function teorilektionPaket(SimplifiedCities $presenter, $citySlug = null)
    {
        if ($citySlug) {
            return $this->index($citySlug, Prices::THEORY_LESSON_CAR, 1, true);
        }
        $cities = $presenter->format($this->cities->getForSelect('schools'));
        return view('shared::search.teorilektion-paket', compact('cities'));
    }

    /**
     * Risktvaan
     *
     * @param SimplifiedCities $presenter
     * @param string $citySlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function risktvaan(SimplifiedCities $presenter, $citySlug = null)
    {
        if ($citySlug) {
            return $this->index($citySlug, Prices::RISK_TWO_CAR);
        }
        $cities = $presenter->format($this->cities->getForSelect('schools'));
        return view('shared::search.risktvaan', compact('cities'));
    }

    /**
     * Mopedkurs
     *
     * @param SimplifiedCities $presenter
     * @param string $citySlug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mopedkurs(SimplifiedCities $presenter, $citySlug = null)
    {
        if ($citySlug) {
            return $this->index($citySlug, Prices::MOPED_PACKAGE, 3);
        }
        $cities = $presenter->format($this->cities->getForSelect('schools'));
        return view('shared::search.mopedkurs', compact('cities'));
    }

    /**
     * Riskettan MC
     *
     * @param SimplifiedCities $presenter
     * @param string $citySlug
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function riskettanmc(SimplifiedCities $presenter, $citySlug = null)
    {
        if ($citySlug) {
            return $this->index($citySlug, Prices::RISK_ONE_MC, 2);
        }
        $cities = $presenter->format($this->cities->getForSelect('schools'));
        return view('shared::search.riskettanmc', compact('cities'));
    }

    /**
     * Risktvaan MC
     *
     * @param SimplifiedCities $presenter
     * @param string $citySlug
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function risktvaanmc(SimplifiedCities $presenter, $citySlug = null)
    {
        if ($citySlug) {
            return $this->index($citySlug, Prices::RISK_TWO_MC, 2);
        }
        $cities = $presenter->format($this->cities->getForSelect('schools'));
        return view('shared::search.risktvaanmc', compact('cities'));
    }

    /**
     * Risktvaan
     *
     * @param SimplifiedCities $presenter
     * @param string $citySlug
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function engelskriskettan(SimplifiedCities $presenter, $citySlug = null)
    {
        if ($citySlug) {
            return $this->index($citySlug, Prices::ENGLISH_RISK_ONE, 1);
        }

        $vehicle = $this->vehicleSegments->query()->where('name', Prices::ENGLISH_RISK_ONE)->first();
        $cities = $presenter->format($this->cities->getForSelect('schools'));

        return view('shared::search.risk', compact('cities', 'vehicle'));
    }

    public function teoriprovOnline(SimplifiedCities $presenter, $citySlug = null)
    {
        if ($citySlug) {
            return $this->index($citySlug, Prices::ENGLISH_RISK_ONE, 1);
        }

        $school = $this->schoolRepo->query()->findOrFail(1663);
        $city = $this->cities->query()->findOrFail(204);
        $course = $this->courses->query()->findOrFail(76518);

        return view('shared::search.teoriprov-online', compact('school', 'city', 'course'));
    }

    /**
     * Get course page by slug name
     *
     * @param SimplifiedCities $presenter
     * @param null $courseSlug
     * @param null $citySlug
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function course(SimplifiedCities $presenter, $slug, $citySlug = null)
    {
        $vehicle = $this->vehicleSegments->query()->where('slug', $slug)->where('bookable', 1)->first();

        if (!$vehicle) {
            throw new NotFoundHttpException();
        }

        if ($citySlug) {
            return $this->index($citySlug, $vehicle->name, $vehicle->vehicle_id);
        }


        $cities = $presenter->format($this->cities->getForSelect('schools'));

        $showModal = false;
        if ($slug === 'mopedam') {
            $showModal = true;
        }

        return view('shared::search.course', compact('cities', 'vehicle', 'showModal'));
    }

    /**
     * Show courses
     *
     * @param $citySlug
     * @param $schoolSlug
     * @param $courseId
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Klarna_Checkout_ApiErrorException
     */
    public function show($citySlug, $schoolSlug, $courseId, Request $request)
    {
        $course = $this->courses->inFuture()->query()->with('school.ratings')->find($courseId);
        if (is_null($course)) {
            return redirect()->route('shared::schools.show', ['citySlug' => $citySlug, 'schoolSlug' => $schoolSlug]);
        }
        $klarnaOrder = null;
        $merchantId = config('klarna.kkj_payment_id');
        $user = Auth::user();
        $this->schemaCourseService->tryParse($course);

        if ($course->school) {
            try {

                if ($request->session()->has('klarna_order_id') && $request->session()->get('merchant_id') == $merchantId) {
                    $klarnaOrderId = $request->session()->get('klarna_order_id');
                    $pendingOrderExists = PendingExternalOrder::where('external_order_id', $klarnaOrderId)->exists();
                    if ($pendingOrderExists) {
                        $klarnaOrder = $this->klarnaService->getOrder($course->school, $klarnaOrderId, $course, true);
                    } else {
                        $klarnaOrder = $this->klarnaService->createOrder($course->school, collect([$course]), $user, [1]);
                    }
                } else {
                    $klarnaOrder = $this->klarnaService->createOrder($course->school, collect([$course]), $user, [1]);
                }
            } catch (\Exception $e) {
                $klarnaOrder = null;
            }

            if (is_null($klarnaOrder)) {
                $request->session()->forget('klarna_order_id');
                $request->session()->forget('merchant_id');
                return redirect()->route('shared::schools.show', ['citySlug' => $citySlug, 'schoolSlug' => $schoolSlug]);
            }

            $request->session()->put('klarna_order_id', $klarnaOrder['id']);
            $request->session()->put('merchant_id', $merchantId);
        }
        return view('shared::courses.show', [
            'course' => $course,
            'schemaCourse' => $this->schemaCourseService->getLdJsonTag(),
            'klarnaOrder' => $klarnaOrder
        ]);
    }

    /**
     * @param $citySlug
     * @param $schoolSlug
     * @param $courseId
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Klarna_Checkout_ApiErrorException
     */
    public function payment($citySlug, $schoolSlug, $courseId, Request $request)
    {
        if (url()->previous() !== route('shared::courses.show', [$citySlug, $schoolSlug, $courseId]) &&
            url()->previous() !== url()->current()) {
            return redirect(url()->previous());
        }

        $course = $this->courses->inFuture()->query()->with('school.ratings')->find($courseId);
        if (is_null($course)) {
            return redirect()->route('shared::schools.show', ['citySlug' => $citySlug, 'schoolSlug' => $schoolSlug]);
        }

        $klarnaOrder = null;
        $merchantId = config('klarna.kkj_payment_id');
        $user = Auth::user();

        if ($course->school) {
            if ($request->session()->has('klarna_order_id') && $request->session()->get('merchant_id') == $merchantId) {
                $klarnaOrderId = $request->session()->get('klarna_order_id');
                $pendingOrderExists = PendingExternalOrder::where('external_order_id', $klarnaOrderId)->exists();

                if ($pendingOrderExists) {
                    $klarnaOrder = $this->klarnaService->getOrder($course->school, $klarnaOrderId, $course, true);
                } else {
                    Log::debug('pendingOrder not exists', [
                        'course' => $course,
                        'user' => $user
                    ]);
                }
            } else {
                Log::debug('klarna_order_id or merchant_id not defined', [
                    'course' => $course,
                    'user' => $user
                ]);
            }

            if (is_null($klarnaOrder)) {
                return redirect()->route('shared::schools.show', compact('citySlug', 'schoolSlug'));
            }
        }

        return view('shared::courses.payment', compact('course', 'klarnaOrder'));
    }

    /**
     * Confirmed
     *
     * @param $citySlug
     * @param $schoolSlug
     * @param $courseId
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function confirmed($citySlug, $schoolSlug, $courseId, Request $request)
    {
        $course = $this->courses->query()->with('school.organization')->findOrFail($courseId);

        $klarnaOrder = null;
        if ($course->school && $request->has('klarna_order_id')) {
            $klarnaOrder = DB::transaction(function () use ($request, $course) {
                $orderId = $request->input('klarna_order_id', null);
                $klarnaOrder = $this->klarnaService->getOrder($orderId, $course);
                $request->session()->forget('klarna_order_id');

                return $klarnaOrder;
            });
        }

        return view('shared::payment.confirmed', [
            'klarnaOrder' => $klarnaOrder,
            'school' => $course->school,
            'citySlug' => $course->city->slug
        ]);
    }

    /**
     * Get Vehicle Segment
     *
     * @param $courseType
     * @param $vehicleId
     * @return null
     */
    protected function getVehicleSegmentId($courseType = null, $vehicleId = 1)
    {
        $segmentId = null;

        if ($courseType) {
            $segments = $this->vehicleSegments->bookable()->get();
            foreach ($segments as $segment) {
                $segmentName = strtolower(trans('vehicle_segments.' . strtolower($segment->name)));
                if ($segmentName && $segment->name == $courseType && $segment->vehicle_id == $vehicleId) {
                    $segmentId = $segment->id;
                    break;
                }
            }

            if (!$segmentId) {
                throw new NotFoundHttpException();
            }
        }

        return $segmentId;
    }

    /**
     * Build title
     *
     * @param $courseType
     * @param $vehicleId
     * @param $city
     * @return string
     */
    protected function buildTitle($courseType, $vehicleId, $city)
    {
        $title1 = '';
        $title2 = '';
        if ($courseType) {
            switch ($courseType) {
                case Prices::INTRODUCTION_CAR:
                    $titles = ['Introduktionskurs', 'handledarkurs '];
                    break;
                case Prices::RISK_ONE_CAR:
                    $titles = ['Riskettan bil', 'Riskettan '];
                    break;
                case Prices::THEORY_LESSON_CAR:
                    $titles = ['Körlektion', 'kurs ', ' idag'];
                    break;
                case Prices::MOPED_PACKAGE:
                    $titles = ['Moppekurs', 'kurs '];
                    break;
                case Prices::INTRODUKTIONSKURS_ENGLISH:
                    $titles = ['Introduction Course English', 'kurs '];
                    break;
                case Prices::RISK_ONE_ARABISKA_CAR:
                    $titles = ['Riskettan Arabiska', 'kurs '];
                    break;
                case Prices::ENGLISH_RISK_ONE:
                    $titles = ['Engelska Riskettan', 'kurs '];
                    break;
                case Prices::SPANISH_RISK_ONE:
                    $titles = ['Riskettan Spanish', 'kurs '];
                    break;
                case Prices::RISK_TWO_MC:
                case Prices::RISK_TWO_CAR:
                    $titles = ['Risktvåan', 'kurs '];
                    break;
                case Prices::RISK_TWO_ARABISKA_CAR:
                    $titles = ['Risktvåan English', 'kurs '];
                    break;
                case Prices::RISK_ONE_TWO_COMBO:
                    $titles = ['Risk 1&2 combo', 'kurs '];
                    break;
                case Prices::RISK_ONE_TWO_COMBO_ENGLISH:
                    $titles = ['Risk 1&2 combo English', 'kurs '];
                    break;
                case Prices::RISK_ONE_MC:
                    $titles = ['Riskettan', 'kurs '];
                    break;
                case Prices::YKB_GRUNDKURS_140_H:
                    $titles = ['YKB Grundkurs 140 h', 'kurs '];
                    break;
                case Prices::YKB_FORTUTBILDNING_35_H:
                    $titles = ['YKB Fortutbildning 35 h', 'kurs '];
                    break;
                default:
                    $titles = ['Körlektion', 'kurs '];
            }
            $title1 .= $titles[0];
            //$title2 .= $titles[1];
        } else {
            $title1 .= 'Körlektion';
            $title2 .= 'kurs ';
        }
        switch ($vehicleId) {
            case 1:
                //$title1 .= ' Bil';
                break;
            case 2:
                $title1 .= ' MC';
                break;
            case 3:
                $title1 .= ' Moped';
                break;
        }
        if ($city)
            $title2 .= 'i ' . $city->name . ' - ' . (isset($titles[1]) ? ucfirst($titles[1]) : '');
        return $title1 . ' ' . $title2;
    }

    /**
     * @param SimplifiedCities $presenter
     * @param Request $request
     * @return string
     */
    protected function getCitySlug(SimplifiedCities $presenter, Request $request)
    {
        $cities = $presenter->format($this->cities->getForSelect('schools'));

        $city = $cities->first(function ($city) use ($request) {
            return $city['id'] == $request->city_id;
        });

        if (empty($city)) {
            $city['slug'] = 'stockholm';
        }

        return $city['slug'];
    }

    /**
     * Search courses
     *
     * @param null $citySlug
     * @param null $courseType
     * @param integer $vehicleId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function index($citySlug = null, $courseType = null, $vehicleId = 1, $inPaketPage = false)
    {
        $courses = $this->courses->isBetween(Carbon::now(), Carbon::now()->endOfDay()->addMonth())->availableSeats();

        $city = $citySlug ? $this->cities->bySlug($citySlug)->first() : null;
        if ($city) {
            $courses = $courses->inCity($city);
        }

        $segmentId = $courseType ? $this->getVehicleSegmentId($courseType, $vehicleId) : null;
        if ($segmentId) {
            $courses = $courses->withinSegment($segmentId);
        }

        $courses = $courses->with('school.city');

        return view('shared::search.index', [
            'title' => $this->buildTitle($courseType, $vehicleId, $city),
            'city' => $city,
            'vehicleId' => $vehicleId,
            'courseType' => $segmentId,
            'resultType' => $inPaketPage ? 'SCHOOL' : static::RESULT_TYPE_COURSE,
            'inPaket' => $inPaketPage,
            'courses' => $courses->get(),
        ]);
    }
}
