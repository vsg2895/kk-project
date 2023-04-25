<?php namespace Organization\Http\Controllers;

use Jakten\Facades\Auth;
use Jakten\Helpers\KlarnaSignup;
use Jakten\Models\{Order, OrderItem};
use Jakten\Repositories\Contracts\{CourseRepositoryContract,
    OrderItemRepositoryContract,
    OrderRepositoryContract,
    PartnerRepositoryContract,
    SchoolClaimRepositoryContract};
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\Payment\Klarna\{KlarnaNativeOnboarding, KlarnaService};
use Shared\Http\Controllers\Controller;

/**
 * Class DashboardController
 * @package Organization\Http\Controllers
 */
class DashboardController extends Controller
{
    /**
     * @var OrderItemRepositoryContract
     */
    private $orderItems;

    /**
     * @var CourseRepositoryContract
     */
    private $courses;

    /**
     * @var KlarnaService
     */
    private $klarnaService;

    /**
     * @var OrderRepositoryContract
     */
    private $orders;

    /**
     * @var SchoolClaimRepositoryContract
     */
    private $schoolClaims;

    /**
     * @var PartnerRepositoryContract
     */
    private $partners;

    /**
     * DashboardController constructor.
     *
     * @param OrderItemRepositoryContract $orderItems
     * @param CourseRepositoryContract $courses
     * @param KlarnaService $klarnaService
     * @param OrderRepositoryContract $orders
     * @param SchoolClaimRepositoryContract $schoolClaims
     * @param PartnerRepositoryContract $partners
     * @param KKJTelegramBotService $botService
     */
    public function __construct(OrderItemRepositoryContract $orderItems, CourseRepositoryContract $courses, KlarnaService $klarnaService, OrderRepositoryContract $orders, SchoolClaimRepositoryContract $schoolClaims, PartnerRepositoryContract $partners, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->orderItems = $orderItems;
        $this->courses = $courses;
        $this->klarnaService = $klarnaService;
        $this->orders = $orders;
        $this->schoolClaims = $schoolClaims;
        $this->partners = $partners;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard()
    {
        $user = Auth::user();
        $user->organization->load('schools.prices', 'schools.organization');

        $bookings = $this->orderItems
            ->forOrganization($user->organization)
            ->isCourseBooking()
            ->query()
            ->limit(4)
            ->orderBy('created_at', 'desc')
            ->get();

        $courses = $this->courses
            ->byOrganization($user->organization)
            ->inFuture()
            ->query()
            ->limit(5)
            ->orderBy('start_time')
            ->get();

        $bookingCount = $this->orderItems
            ->forOrganization($user->organization)
            ->isCourseBooking()
            ->count();

        $orders = $this->orders->forOrganization($user->organization)->query()->with('items')->get();

        $orderValue = $orders->reduce(function ($total, Order $order) {
            return $total + $order->items->sum(function (OrderItem $orderItem) {
                    return $orderItem->amount * $orderItem->quantity;
                });
        }, 0);

        $claims = $this->schoolClaims->byOrganization($user->organization)->query()->with('school')->get();

        $onboarding = null;
        if ($user->organization->sign_up_status == KlarnaSignup::STATUS_NOT_INITIATED) {
            $klarnaOnboarding = new KlarnaNativeOnboarding($user->organization);
            $onboarding = $klarnaOnboarding->data;
        }

        $partners = $this->partners->isActive()->query()->get();

        return view('organization::dashboard', compact(
            'user', 'bookings', 'courses',
            'bookingCount', 'orderValue', 'claims',
            'onboarding', 'partners'
        ));
    }

    /**
     * testNative
     */
    public function testNative()
    {
        $this->klarnaService->nativeOnboarding(Auth::user()->organization);
    }
}
