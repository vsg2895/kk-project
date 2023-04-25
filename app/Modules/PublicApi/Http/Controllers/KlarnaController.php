<?php namespace PublicApi\Http\Controllers;

use Jakten\Events\NewOrder;
use Api\Http\Controllers\ApiController;
use Illuminate\{Database\Eloquent\ModelNotFoundException, Http\Request, Support\Facades\Log};
use Jakten\Models\{Course, PendingExternalOrder, School, User};
use Jakten\Events\OrderFailed as EventsOrderFailed;
use Jakten\Exceptions\CourseFullException;
use Jakten\Jobs\ActivatePackageOrders;
use Jakten\Repositories\Contracts\CourseRepositoryContract;
use Jakten\Services\{GiftCardService, KKJTelegramBotService, Payment\Klarna\KlarnaService};
use Exception;

/**
 * Class KlarnaController
 * @package PublicApi\Http\Controllers
 */
class KlarnaController extends ApiController
{
    /**
     * @var CourseRepositoryContract
     */
    private $courses;

    /**
     * @var KlarnaService
     */
    private $klarnaService;

    /**
     * @var User
     */
    private $user;

    /**
     * @var User
     */
    private $giftCardService;

    /**
     * KlarnaController constructor.
     *
     * @param CourseRepositoryContract $courses
     * @param KlarnaService $klarnaService
     * @param GiftCardService $giftCardService
     * @param User $user
     * @param KKJTelegramBotService $botService
     */
    public function __construct(CourseRepositoryContract $courses, KlarnaService $klarnaService, GiftCardService $giftCardService, User $user = null, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);

        $this->user = $user;
        $this->courses = $courses;
        $this->klarnaService = $klarnaService;
        $this->giftCardService = $giftCardService;
    }

    /**
     * Show order by $schoolId and $klarnaOrderId
     *
     * @param null $schoolId
     * @param Request $request
     */
    public function show($schoolId = null, Request $request)
    {
        $klarnaOrderId = $request->input('klarna_order_id');
        $school = $schoolId ? School::whereId($schoolId)->first() : null;
        try {
            $klarnaOrder = $this->klarnaService->getOrder($school, $klarnaOrderId, true);

            // Log remote request from Klarna
            //Log::debug(var_export($klarnaOrder['data'], true));
            print_r($klarnaOrder);
        } catch (Exception $e) {
            Log::debug('Read previous message!');
        }
    }

    /**
     * @param Request $request
     * @param null|int|string $schoolId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Klarna_Checkout_ApiErrorException
     * @throws \Jakten\Exceptions\CourseFullException
     * @throws ModelNotFoundException
     * @throws \KlarnaException
     */
    public function push(Request $request, int $schoolId = null)
    {
        $klarnaOrderId = $request->query('klarna_order_id');

        /** @var School $school */
        $school = School::where('id', $schoolId)->first();

        $klarnaOrder = $this->klarnaService->getOrder($school, $klarnaOrderId);

        $this->klarnaService->setCreator($klarnaOrder);
        $firstCourseId = null;

        /** @var KKJTelegramBotService $kkjBot */
        $kkjBot = resolve(KKJTelegramBotService::class);

        try {
            $pendingOrder = PendingExternalOrder::where('external_order_id', $klarnaOrderId)->first();

            if ($pendingOrder instanceof PendingExternalOrder) {
                $order = $this->klarnaService->confirmOrder($klarnaOrder, $schoolId);

                try {
                    //Load fresh items relationships for just created model
                    $order->load('items');

                    //activate booking fee
                    $klarnaOrder->fetch();
                    $bookingItem = [];
                    $rebookingItem = [];
                    $saldoAmount = 0;
                    foreach ($klarnaOrder['order_lines'] as $item) {
                        if ($item['name'] == config('fees.booking_fee_to_kkj_name') || $item['name'] == config('klarna.gift_cart_name') || $item['name'] == config('klarna.rebooking')
                            || $item['name'] == config('klarna.theory_discount')) {
                            $bookingItem[] = $item;
                        }

                        if ($item['name'] == config('klarna.rebooking')) {
                            $rebookingItem = $item;
                            $saldoAmount = $item['unit_price'];
                        }
                    }

                    $kkjBot->log('urgent_order_activation', ['order_id' => $order->id]);
                    $this->klarnaService->captureOrderItems($order, $order->items, $bookingItem);

                    $klarnaOrder->updateMerchantReferences([
                        "merchant_reference1" => 'Order '. $order->id .' '. $order->school_id
                    ]);

                    if (count($rebookingItem)) {
                        $order->saldo_used = true;
                        $order->saldo_amount = $saldoAmount / 100;
                        $order->save();
                    }
                } catch (\Exception $e) {
                    $kkjBot->log('urgent_order_activation_failed',
                        [
                            'order_id' => $order->id,
                            'message' => $e->getMessage()
                        ]);

                    Log::warning('Error of urgent activation Klarna order: ' . $order->external_order_id, [
                        'message' => $e->getMessage()
                    ]);
                }

                if ($giftCardsUsed = $pendingOrder->getGiftCardsUsed()) {
                    $this->giftCardService->consumeForOrder($order, $giftCardsUsed);
                }

                $pendingOrder->delete();

                // Trigger event NewOrder
                event(new NewOrder($order));
                $this->botService->log('klarna_order_create', [
                    'order_id' => $order->id,
                    'reservation_id' => $order->external_reservation_id
                ], env('APP_URL'));

                return $this->success();
            }

        } catch (Exception $e) {
            $pendingExternalOrders = PendingExternalOrder::whereExternalOrderId($klarnaOrderId)->first();
            $firstCourseId = current($pendingExternalOrders->getCourseIds());
            $placeholderCourse = Course::whereId($firstCourseId)->first();
            $user = $this->user->query()->where('email', $klarnaOrder['billing_address']['email'])->firstOrFail();

            $kkjBot->log('failed_confirmation_with_klarna_push', compact('klarnaOrder'));

            Log::error('Klarna push Exception', ["klarna_order_id" => $klarnaOrderId, "message" => $e->getMessage()]);
            event(new EventsOrderFailed($placeholderCourse, $klarnaOrder['reservation'], $user));
            $this->botService->log('order_create_failed', compact('pendingExternalOrders'));

            return $this->success();
        }

        return $this->error(['success' => false], 409);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function onboardingResponse(Request $request)
    {
        $context = [
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'content' => $request->getContent(),
        ];

        if ($request->hasHeader('Content-Type')) {
            $context['content-type'] = $request->header('Content-Type');
        }

        Log::debug('Onboarding answer', $context);

        $resourceUrl = $request->getContent();
        $this->klarnaService->handleOnboardingResponse($resourceUrl);

        return $this->success();
    }

    /**
     * @param Request $request
     */
    public function onboardingUpdate(Request $request)
    {
        $context = [
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'content' => $request->getContent(),
        ];

        if ($request->hasHeader('Content-Type')) {
            $context['content-type'] = $request->header('Content-Type');
        }

        Log::debug('Onboarding update', $context);

        $data = json_decode($request->getContent(), true);
        $this->klarnaService->updateOnboarding($data);
    }
}
