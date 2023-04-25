<?php namespace Student\Http\Controllers;

use Illuminate\Support\Carbon;
use Jakten\Facades\Auth;
use Jakten\Models\Benefit;
use Jakten\Models\CourseParticipant;
use Jakten\Models\OrderItem;
use Jakten\Repositories\Contracts\OrderItemRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\StudentLoyaltyProgramService;
use Shared\Http\Controllers\Controller;

/**
 * Class BookingController
 * @package Student\Http\Controllers
 */
class BookingController extends Controller
{
    /**
     * @var OrderItemRepositoryContract
     */
    private $orderItems;

    /**
     * @var Benefit
     */
    private $benefit;

    /**
     * BookingController constructor.
     *
     * @param OrderItemRepositoryContract $orderItems
     * @param KKJTelegramBotService $botService
     */
    public function __construct(OrderItemRepositoryContract $orderItems, KKJTelegramBotService $botService, Benefit $benefit)
    {
        parent::__construct($botService);
        $this->orderItems = $orderItems;
        $this->benefit = $benefit;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $bookings = $this->orderItems->byUser(Auth::user())->isCourseBooking()->active()->paginate();//check deleted at works
        $benefits = $this->benefit->where('user_id', Auth::id())->where('applied', true)->get();
        $paketBenefit = $benefits->where('addon_id', '!=', null)->first();
        $loyaltyData = [];

        $segmentBenefits = StudentLoyaltyProgramService::SEGMENT_BENEFITS;
        $benefits = $benefits->groupBy('vehicle_segment_id')->toArray();
        foreach (StudentLoyaltyProgramService::GARAGES as $garage) {
            if ($garage === 16) {
                $loyaltyData[$garage]['balance'] = $segmentBenefits[$garage]['balance'];
                $loyaltyData[$garage]['discount'] = $segmentBenefits[$garage]['discount'];
                $loyaltyData[$garage]['name'] = $segmentBenefits[$garage]['name'];
                $loyaltyData[$garage]['open'] = array_key_exists($garage, $benefits) || array_key_exists(16, $benefits);
            } elseif($garage === 0) {
                $loyaltyData[$garage]['balance'] = StudentLoyaltyProgramService::PACKAGE_BENEFIT['balance'];
                $loyaltyData[$garage]['discount'] = StudentLoyaltyProgramService::PACKAGE_BENEFIT['discount'];
                $loyaltyData[$garage]['name'] = 'Körkortspaket';
                $loyaltyData[$garage]['open'] = (boolean)$paketBenefit;
                $loyaltyData[$garage]['url'] = route('shared::teorilektion.paket');
            } elseif($garage === 6 || $garage === 13) {
                $loyaltyData[$garage]['balance'] = $segmentBenefits[$garage]['balance'];
                $loyaltyData[$garage]['discount'] = $segmentBenefits[$garage]['discount'];
                $loyaltyData[$garage]['name'] = $segmentBenefits[$garage]['name'];
                $loyaltyData[$garage]['open'] = array_key_exists($garage, $benefits) || array_key_exists(26, $benefits);
                $loyaltyData[$garage]['url'] = $segmentBenefits[$garage]['url'];
            } else {//normal course
                $loyaltyData[$garage]['balance'] = $segmentBenefits[$garage]['balance'];
                $loyaltyData[$garage]['discount'] = $segmentBenefits[$garage]['discount'];
                $loyaltyData[$garage]['name'] = $segmentBenefits[$garage]['name'];
                $loyaltyData[$garage]['open'] = array_key_exists($garage, $benefits);
                $loyaltyData[$garage]['url'] = $segmentBenefits[$garage]['url'];
            }
        }

        return view('student::bookings.index', [
            'bookings' => $bookings,
            'loyaltyData' => $loyaltyData,
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        $booking = $this->orderItems->query()->findOrFail($id);
        $this->authorize('view', $booking);

        return view('student::bookings.show', [
            'booking' => $booking,
        ]);
    }
}
