<?php namespace Shared\Http\Controllers;

use Carbon\Carbon;
use Jakten\Helpers\Roles;
use Illuminate\Http\Request;
use Jakten\Presenters\SimplifiedCities;
use Jakten\Models\{Order, CourseParticipant};
use Jakten\Repositories\Contracts\{CityRepositoryContract, CourseRepositoryContract, VehicleRepositoryContract};
use Illuminate\Support\Facades\Cache;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class IndexController
 * @package Shared\Http\Controllers
 */
class IndexController extends Controller
{
    /**
     * Orders selected for days before
     */
    const ORDERS_FOR_DAYS = 30;

    /**
     * @var CityRepositoryContract
     */
    private $cities;

    /**
     * @var VehicleRepositoryContract
     */
    private $vehicles;

    /**
     * @var CourseRepositoryContract
     */
    private $courses;

    /**
     * IndexController constructor.
     *
     * @param CityRepositoryContract $cities
     * @param VehicleRepositoryContract $vehicles
     * @param CourseRepositoryContract $courses
     * @param KKJTelegramBotService $botService
     */
    public function __construct(CityRepositoryContract $cities, VehicleRepositoryContract $vehicles, CourseRepositoryContract $courses, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->cities = $cities;
        $this->vehicles = $vehicles;
        $this->courses = $courses;
    }

    /**
     * @param SimplifiedCities $presenter
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(SimplifiedCities $presenter)
    {
        $cities = $presenter->format($this->cities->getForSelect('schools'));
        $vehicles = $this->vehicles->query()->get();

        $latestCourses = $this->courses
            ->recentlyBooked()
            ->inFuture()
            ->availableSeats()
            ->query()
            ->limit(3)
            ->with('school.city', 'segment')
            ->groupBy(['courses.id'])
            ->select(['courses.*', \DB::raw('MAX(course_participants.created_at) as booked')])
            ->get();

        $interval = [Carbon::now()->subDays(self::ORDERS_FOR_DAYS), Carbon::now()];

        $ordersCounter = Order::whereBetween('created_at', $interval)
                ->count() + CourseParticipant::where('type', Roles::$roles[Roles::ROLE_STUDENT])
                ->whereBetween('created_at', $interval)
                ->count();

        $orderCounter = str_split($ordersCounter);

        $xmasCampaign = true;
        $bonus = intval(round(100 * (floatval(Cache::get('GIFT_CARD_INCREASED_VALUE', 1)) - 1)));

        return view('shared::index_grafikfabriken', compact('cities', 'vehicles', 'latestCourses', 'orderCounter', 'xmasCampaign', 'bonus'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function search(Request $request)
    {
        $city = $this->cities->query()->find($request->input('city_id'));
        $vehicleId = $request->input('vehicle_id', 1);

        if (!$city) {
            $url = route('shared::schools.index');
            $url .= '?vehicle_id=' . $vehicleId;

            return redirect($url);
        }

        return redirect()->route('shared::search.schools', [
            'slug' => $city->slug,
            'vehicle_id' => $request->input('vehicle_id', 1),
        ]);
    }
}
