<?php namespace Jakten\Services\Payment\Klarna;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Jakten\Events\NewRegistration;
use Jakten\Exceptions\CourseFullException;
use Jakten\Facades\Auth;
use Klarna\Rest\Transport\Exception\ConnectorException;
use Jakten\Helpers\{KlarnaError, KlarnaSignup, Payment};
use Jakten\Models\{Course, GiftCard, Order, OrderItem, Organization, PendingExternalOrder, School, User};
use Jakten\Repositories\Contracts\{GiftCardTypeRepositoryContract,
    OrganizationRepositoryContract,
    UserRepositoryContract};
use Jakten\Services\{Annotation\AnnotationService,
    Asset\AnnotationType,
    GiftCardService,
    KKJTelegramBotService,
    OrderService,
    StudentLoyaltyProgramService,
    UserService};
use Klarna_Checkout_ApiErrorException;
use Klarna_Checkout_Order;
use KlarnaException;
use KlarnaFlags;
use \Klarna\Rest\OrderManagement\Order as Klarna_Order;
use phpDocumentor\Reflection\Types\Object_;

require_once base_path('vendor/klarna/checkout/src/Klarna/Checkout.php');
require_once base_path('vendor/klarna/php-xmlrpc/src/Klarna.php');
require_once base_path('vendor/klarna/php-xmlrpc/src/KlarnaCountry.php');
require_once base_path('vendor/klarna/php-xmlrpc/src/KlarnaLanguage.php');
require_once base_path('vendor/klarna/php-xmlrpc/src/KlarnaCurrency.php');

/**
 * Class KlarnaService
 * @package Jakten\Services\Payment\Klarna
 */
class KlarnaService
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * @var AnnotationService
     */
    private $annotationService;

    /**
     * @var UserRepositoryContract
     */
    private $users;

    /**
     * @var OrganizationRepositoryContract
     */
    private $organizations;

    /**
     * @var GiftCardTypeRepositoryContract
     */
    private $giftCardTypes;

    /**
     * @var \Klarna\Rest\Transport\GuzzleConnector
     */
    private $connector;

    /**
     * @var StudentLoyaltyProgramService
     */
    private $studentLoyaltyProgramService;

    /**
     * KlarnaService constructor.
     *
     * @param UserService $userService
     * @param OrderService $orderService
     * @param UserRepositoryContract $users
     * @param OrganizationRepositoryContract $organizations
     * @param AnnotationService $annotationService
     * @param GiftCardTypeRepositoryContract $giftCardTypes
     */
    public function __construct(UserService $userService, OrderService $orderService, UserRepositoryContract $users,
                                OrganizationRepositoryContract $organizations, AnnotationService $annotationService,
                                GiftCardTypeRepositoryContract $giftCardTypes, GiftCardService $giftCardService,
                                StudentLoyaltyProgramService $studentLoyaltyProgramService)
    {
        $this->userService = $userService;
        $this->orderService = $orderService;
        $this->users = $users;
        $this->baseUrl = config('klarna.base_url');
        $this->organizations = $organizations;
        $this->annotationService = $annotationService;
        $this->giftCardTypes = $giftCardTypes;
        $this->giftCardService = $giftCardService;

        $merchantId = env('KLARNA_V3_KKJ_PAYMENT_ID');
        $secret = env('KLARNA_V3_KKJ_PAYMENT_SECRET');
        $apiEndpoint = env('APP_ENV') === 'production' ? \Klarna\Rest\Transport\ConnectorInterface::EU_BASE_URL : \Klarna\Rest\Transport\ConnectorInterface::EU_TEST_BASE_URL;

        $this->connector = \Klarna\Rest\Transport\GuzzleConnector::create(
            $merchantId,
            $secret,
            $apiEndpoint
        );
        $this->studentLoyaltyProgramService = $studentLoyaltyProgramService;
    }


    /**
     * @param School $school
     * @param Collection $courses
     * @param User|null $user
     * @param array $students
     * @param array $tutors
     * @param array $addons
     * @return Klarna_Checkout_Order|null
     */
    public function createOrder(School $school, Collection $courses, User $user = null, $students = [], $tutors = [], $addons = [], $customAddons = [])
    {
        $merchantId = env('KLARNA_V3_KKJ_PAYMENT_ID');

        $klarnaOrderData = new KlarnaCheckoutOrder($merchantId);
        $klarnaOrderData->setSchoolOrderData($school, $courses, $addons, $customAddons);
        $klarnaOrderData->setBillingAddressData($user);


        $klarnaOrderData = $this->addCartItems($klarnaOrderData, $courses, $students, $tutors, $addons, $customAddons, $user);

        $klarnaOrder = new \Klarna\Rest\Checkout\Order($this->connector);

        /** @var KKJTelegramBotService $kkjBot */
        $kkjBot = resolve(KKJTelegramBotService::class);

        try {

            $user_id = Auth::id();
            $kkjBot->log('order_create', compact('user_id', 'courses', 'students', 'tutors', 'addons', 'customAddons'));

            $orderData = $klarnaOrderData->getData();
            $klarnaOrder->create($orderData);
            $klarnaOrder->fetch();

            $klarnaOrder['id'] = $klarnaOrder['order_id'];

            $pendingOrder = new PendingExternalOrder([
                'user_id' => $user ? $user->id : null,
                'external_order_id' => $klarnaOrder['order_id'],
            ]);
            $pendingOrder->setData($courses, $students, $tutors, $addons, $customAddons);
            $pendingOrder->save();

            Log::debug('Pending Klarna order created with id: ' . $klarnaOrder['id'] . ', ' . var_export($orderData, true));

            return $klarnaOrder;
        } catch (\Exception $e) {
            $kkjBot->log('order_create_failed', $e->getMessage());

            Log::error('Error of creating pending Klarna order', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param User|null $user
     * @return \Klarna\Rest\Checkout\Order
     */
    public function createGiftCardOrder(User $user = null)
    {
        $merchantId = env('KLARNA_V3_KKJ_PAYMENT_ID');
        $klarnaOrderData = new KlarnaCheckoutOrder($merchantId);
        $klarnaOrder = new \Klarna\Rest\Checkout\Order($this->connector);

        try {
            $orderData = $klarnaOrderData->getData();
            $klarnaOrder->create($orderData);
            $klarnaOrder->fetch();

            $pendingOrder = new PendingExternalOrder([
                'user_id' => $user ? $user->id : null,
                'external_order_id' => $klarnaOrder['order_id'],
            ]);

            $pendingOrder->setGiftCardData([]);
            $pendingOrder->save();

            Log::debug('Pending Gift Klarna order created with id: ' . $klarnaOrder['order_id'] . ', ' . var_export($orderData, true));

            return $klarnaOrder;
        } catch (Klarna_Checkout_ApiErrorException $e) {
            Log::error('Error of creating Gift Klarna order', [
                'message' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * @param KlarnaCheckoutOrder $order
     * @param Collection $courses
     * @param array $students
     * @param array $tutors
     * @param array $addons
     * @return KlarnaCheckoutOrder
     */
    protected function addCartItems(
        KlarnaCheckoutOrder $order,
        Collection &$courses,
        array &$students,
        array &$tutors,
        array &$addons,
        array &$customAddons,
        User $user = null
    ) {

        foreach ($courses as $course) {
            /** @var Course $course */
            foreach ($students as $index => &$student) {
                if (!is_array($student)) {
                    continue;
                }
                if (!isset($student["courseId"]) || $student["courseId"] !== $course->id) {
                    continue;
                }

                $price = $course->price * 100;

                //calculate theory discount if benefit exists
                if ($user && in_array($course->vehicle_segment_id, StudentLoyaltyProgramService::BENEFICIARY_SEGMENT_IDS)) {
                    $benefit = $this->studentLoyaltyProgramService->checkTheoryDiscount($user, $course);

                    if ($benefit) {
                        $price = $price - $price * $benefit->amount / 100;
                    }
                }

                $student['id'] = str_random();
                $item = new KlarnaCartItem([
                    'reference' => $student['id'],
                    'name' => $course->name,
                    'quantity' => 1,
                    'price' => $price,
                ]);
                $order->addCartItem($item);
            }

            foreach ($tutors as $index => &$tutor) {

                if (!is_array($tutor)) {
                    continue;
                }

                if (!isset($tutor["courseId"]) || $tutor["courseId"] !== $course->id) {
                    continue;
                }

                $tutor['id'] = str_random();
                $item = new KlarnaCartItem([
                    'reference' => $tutor['id'],
                    'name' => $course->name,
                    'quantity' => 1,
                    'price' => $course->price * 100,
                ]);
                $order->addCartItem($item);
            }
        }

        foreach ($addons as &$addon) {
            if (!isset($addon['id']) || !$addon['id']) {
                $addon['id'] = str_random();
            }

            $addon['local_id'] = isset($addon['local_id']) ? $addon['local_id'] : $addon['id'];

            $item = new KlarnaCartItem([
                'reference' => $addon['id'],
                'name' => $addon['name'],
                'quantity' => $addon['quantity'],
                'price' => $addon['price'] * 100,
            ]);
            $order->addCartItem($item);
        }

        foreach ($customAddons as &$customAddon) {

            if (!isset($customAddon['id']) || !$customAddon['id']) {
                $customAddon['id'] = str_random();
            }

            $customAddon['local_id'] = isset($customAddon['local_id']) ? $customAddon['local_id'] : $customAddon['id'];

            $item = new KlarnaCartItem([
                'reference' => $customAddon['id'],
                'name' => $customAddon['name'],
                'quantity' => $customAddon['quantity'],
                'price' => $customAddon['price'] * 100,
            ]);
            $order->addCartItem($item);
        }

        $item = new KlarnaCartItem([
            'reference' => str_random(),
            'name' => config('fees.booking_fee_to_kkj_name'),
            'quantity' => 1,
            'price' => config('fees.booking_fee_to_kkj') * 100,
        ]);
        $order->addCartItem($item);

        return $order;
    }

    /**
     * @param KlarnaCheckoutOrder $order
     * @param $giftCardTypeIds
     * @return KlarnaCheckoutOrder
     */
    protected function addGiftCards(KlarnaCheckoutOrder $order, $giftCardTypeIds)
    {
        $giftCardTypes = $this->giftCardTypes->getByIds($giftCardTypeIds);

        foreach ($giftCardTypes as $giftCardType) {

            $item = new KlarnaCartItem([
                'reference' => str_random(),
                'name' => $giftCardType['name'],
                'quantity' => 1,
                'price' => $giftCardType['price'] * 100,
            ]);
            $order->addCartItem($item);
        }
        return $order;
    }

    /**
     * @param $orderId
     *
     * @param $secret
     * @param array $courseIds
     * @param User|null $user
     * @param array $students
     * @param array $tutors
     * @param array $addons
     * @param array $customAddons
     * @param null $giftCardToken
     * @return Klarna_Checkout_Order
     * @throws Klarna_Checkout_ApiErrorException
     */
    public function updateOrder($orderId, $secret, array $courseIds, User $user = null, $students = [], $tutors = [], $addons = [], $customAddons = [], $giftCardTokens = [])
    {
        $klarnaOrder = new \Klarna\Rest\Checkout\Order($this->connector, $orderId);

        $courses = Course::whereIn('id', $courseIds)->get();
        $giftCards = new Collection();
        if (count($giftCardTokens)) {
            $giftCardTokens = array_unique($giftCardTokens);
            foreach ($giftCardTokens as $giftCardToken) {
                $giftCard = GiftCard::query()->where('token', $giftCardToken)->first();
                if ($giftCard) {
                    $giftCards->push($giftCard);
                }
            }
        }
        $giftCardsToUse = null;

//        try {
            $klarnaOrder->fetch();

            $merchantId = env('KLARNA_V3_KKJ_PAYMENT_ID');
            $klarnaOrderData = new KlarnaCheckoutOrder($merchantId);

            $klarnaOrderData->setSchoolOrderData(null, $courses, $addons, $customAddons);
            $klarnaOrderData->setBillingAddressData($user);
            $klarnaOrderData = $this->addCartItems($klarnaOrderData, $courses, $students, $tutors, $addons, $customAddons, $user);

            $data = $klarnaOrderData->getData();

            if ($giftCards->count()) {
                $giftCardsToUse = new Collection();
                $maximumGiftCardBalanceToUse = $data['order_amount'] / 100;

                foreach ($giftCards as $giftCard) {
                    if (!$maximumGiftCardBalanceToUse) {
                        break;
                    }
                    if ($maximumGiftCardBalanceToUse > $giftCard->remaining_balance) {
                        $percentToUse = 0;

                        if (in_array((int)$giftCard->gift_card_type_id, \Jakten\Models\GiftCardType::PERCENT_TYPES)) {
                            $percentToUse = (($data['order_amount'] / 100) - config('fees.booking_fee_to_kkj')) * ($giftCard->remaining_balance/100);
                        }

                        $giftCardsToUse->push([
                            'gift_card_type_id' => $giftCard->gift_card_type_id,
                            'token' => $giftCard->token,
                            'balance_to_use' => $percentToUse ?: $giftCard->remaining_balance
                        ]);
                        $maximumGiftCardBalanceToUse = $percentToUse ? $maximumGiftCardBalanceToUse - $percentToUse : $maximumGiftCardBalanceToUse - $giftCard->remaining_balance;
                    } else {
                        $giftCardsToUse->push([
                            'gift_card_type_id' => $giftCard->gift_card_type_id,
                            'token' => $giftCard->token,
                            'balance_to_use' => $maximumGiftCardBalanceToUse
                        ]);
                        $maximumGiftCardBalanceToUse = 0;
                    }
                }

                foreach ($giftCardsToUse as $giftCardToUse) {
                    $price = -($giftCardToUse['balance_to_use'] * 100);
                    $item = new KlarnaCartItem(
                        [
                            'type' => 'discount',
                            'reference' => $giftCardToUse['token'],
                            'name' => 'Presentkort',
                            'quantity' => 1,
                            'price' => $price,
                        ],
                        true
                    );
                    $data['order_lines'][] = $item->data;
                    $data['order_amount'] += $item->data['total_amount'];
                    $data['order_tax_amount'] += $item->data['total_tax_amount'];
                }
            }

            if ($user) {
                $saldo = round((float)$user->amount, 2) * 100;
                if ($saldo > 0) {
                    if ($data['order_amount'] > $saldo) {
                        $price = -$saldo;
                    } else {
                        $price = -$data['order_amount'];
                    }

                    $item = new KlarnaCartItem(
                        [
                            'type' => 'discount',
                            'reference' => $user->id,
                            'name' => config('klarna.rebooking'),
                            'quantity' => 1,
                            'price' => $price,
                        ],
                        true
                    );
                    $data['order_lines'][] = $item->data;
                    $data['order_amount'] += $item->data['total_amount'];
                    $data['order_tax_amount'] += $item->data['total_tax_amount'];
                }


            }

            if (count($data['order_lines']) > 1) {
                $klarnaOrder->update([
                    'order_amount' => $data['order_amount'],
                    'order_tax_amount' => $data['order_tax_amount'],
                    'order_lines' => $data['order_lines'],
                    'attachment' => $data['attachment']
                ]);
                $pendingOrder = PendingExternalOrder::where('external_order_id', $orderId)->firstOrFail();
                $klarnaOrder['id'] = $klarnaOrder['order_id'];
                $pendingOrder->setData($courses, $students, $tutors, $addons, $customAddons, $giftCardsToUse);
                $pendingOrder->save();

            } else {
                throw new Klarna_Checkout_ApiErrorException('No items in order', 500);
            }

            return $klarnaOrder;
//        } catch (Klarna_Checkout_ApiErrorException $e) {
//            Log::error('Error when updating Klarna order', [
//                'message' => $e->getMessage(),
//                'payload' => $e->getPayload(),
//            ]);
//            throw $e;
//        }
    }

    /**
     * @param $orderId
     * @param $giftCardType
     * @param User|null $user
     * @return \Klarna\Rest\Checkout\Order
     * @throws Exception
     */
    public function updateGiftCardOrder($orderId, $giftCardType, User $user = null)
    {
        $klarnaOrder = new \Klarna\Rest\Checkout\Order($this->connector, $orderId);

        $merchantId = env('KLARNA_V3_KKJ_PAYMENT_ID');
        $klarnaOrderData = new KlarnaCheckoutOrder($merchantId);
        $giftCardType = $this->giftCardTypes->getById($giftCardType['id']);
        $emptyCollection = new Collection();
        $emptyArray = [];

        $klarnaOrderData = $this->addCartItems($klarnaOrderData, $emptyCollection, $emptyArray, $emptyArray, $emptyArray, $emptyArray, $user);

        try {
            $klarnaOrder->fetch();

            $id = str_random();
            $item = new KlarnaCartItem([
                'reference' => $id,
                'name' => $giftCardType['name'],
                'quantity' => 1,
                'price' => $giftCardType['price'] * 100,
            ]);

            $klarnaOrderData->addCartItem($item);
            $data = $klarnaOrderData->getData();

            $klarnaOrder->update($data);

            $pendingOrder = PendingExternalOrder::query()->where('external_order_id', $orderId)->firstOrFail();
            $giftCardType['external_id'] = $id;
            $pendingOrder->setGiftCardData($giftCardType);
            $pendingOrder->user_id = $user ? $user->id : null;

            $pendingOrder->save();

            return $klarnaOrder;
        } catch (Exception $e) {
            Log::error('Error when updating Klarna order', [
                'message' => $e->getMessage(),
                'payload' => $e->getPayload(),
            ]);
            throw $e;
        }
    }

    /**
     * @param School $school
     * @param $klarnaOrderId
     * @param bool $checkout
     * @return Klarna_Order
     * @throws Klarna_Checkout_ApiErrorException
     */
    public function getOrder(School $school = null, $klarnaOrderId, $checkout = false)
    {
        try {
            if (substr_count($klarnaOrderId, '-') === 0) {
                $secret = config('klarna.kkj_payment_secret');
                $connector = \Klarna_Checkout_Connector::create($secret, $this->baseUrl);
                $order = new Klarna_Checkout_Order($connector, $klarnaOrderId);
            } else {
                $order = $checkout ? new \Klarna\Rest\Checkout\Order($this->connector, $klarnaOrderId) : new \Klarna\Rest\OrderManagement\Order($this->connector, $klarnaOrderId);
            }

            $order->fetch();
        } catch (\Exception $e) {
            Log::error('Error of getting Klarna order', [
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }

        if ($order['status'] == 'checkout_incomplete' && !$checkout) {
            Log::warning('Tried to fetch order while not complete', ['order_id' => $klarnaOrderId]);
        }

        isset($order['id']) ?: $order['id'] = $order['order_id'];

        return $order;
    }

    /**
     * @param Klarna_Order $klarnaOrder
     */
    public function setCreator( $klarnaOrder)
    {
        $pendingOrder = PendingExternalOrder::where('external_order_id', $klarnaOrder['id'])->firstOrFail();
        // $pendingOrder->reservation_id = $klarnaOrder['reservation'];
        $billingPerson = new KlarnaBillingPerson($klarnaOrder['billing_address']);

        if (!$pendingOrder->user_id) {
            $loggedInUser = $this->users->query()->where('email', $billingPerson->getEmail())->firstOr(function () use ($billingPerson) {
                $user = $this->userService->storeUser(with(new FormRequest($billingPerson->toUserData())));
                event(new NewRegistration($user));
                return $user;
            });

            $pendingOrder->user_id = $loggedInUser->id;
            $pendingOrder->save();
        }
    }

    /**
     * @param Klarna_Order $klarnaOrder
     * @param null $schoolId
     * @return \Illuminate\Database\Eloquent\Model|Order|null
     * @throws CourseFullException
     * @throws \Exception
     */
    public function confirmOrder(Klarna_Order $klarnaOrder, $schoolId = null)
    {
        /** @var PendingExternalOrder $pendingOrder */
        $pendingOrder = PendingExternalOrder::query()->where('external_order_id', $klarnaOrder['id'])->firstOrFail();
        $order = null;

        if ($klarnaOrder['status'] == 'AUTHORIZED') {
            $request = new FormRequest(array_merge($pendingOrder->data, [
                'external_order_id' => $pendingOrder->external_order_id,
                'payment_method' => Payment::KLARNA,
                'external_reservation_id' => $klarnaOrder['klarna_reference'],
            ]));

            $order = $schoolId ?
                $this->confirmCourseOrder($klarnaOrder, $pendingOrder, $request, $schoolId) :
                $this->confirmGiftCardOrder($klarnaOrder, $pendingOrder, $request);

            try {
                $expiresAt = new Carbon($klarnaOrder['expires_at']);
                $order->update(['klarna_expires_at' => $expiresAt]);
            } catch (\Exception $e) {
                Log::error('Klarna expires_at error', [
                    'id' => $klarnaOrder['id'],
                    'expires_at' => $klarnaOrder['expires_at']
                ]);
            }

            return $order;
        } else {
            Log::error('Klarna checkout not completed but tried to show confirmation', [
                'id' => $klarnaOrder['id'],
            ]);

            throw new \BadMethodCallException();
        }
    }

    /**
     * @param Klarna_Order $klarnaOrder
     * @param PendingExternalOrder $pendingOrder
     * @param FormRequest $request
     * @param int $schoolId
     * @return \Illuminate\Database\Eloquent\Model|Order
     * @throws CourseFullException
     */
    private function confirmCourseOrder(Klarna_Order $klarnaOrder, PendingExternalOrder $pendingOrder, FormRequest $request, int $schoolId)
    {
        $courseIds = $pendingOrder->getCourseIds();
        $courses = $courseIds ? Course::query()->whereIn('id', $courseIds)->get() : Collect([]);
        try {
            Log::debug('Start order creation (UserId: ' . $pendingOrder->user->id . ', SchoolId: ' . $schoolId . ')');
            $order = $this->orderService->storeOrder($request, $courses, $pendingOrder->user, $schoolId);
            Log::debug('Finish order creation: ' . $order->id . ' (' . $order->external_order_id . '), details: ' . var_export($order->attributesToArray(), true));

            try {
                // At this point make sure the order is created in your system and send a
                // confirmation email to the customer
                // At this point make sure the order
                $klarnaOrder->acknowledge();
            } catch (Exception $e) {
                Log::warning('Error of updating remote Klarna order: ' . $klarnaOrder['id'], [
                    'message' => $e->getMessage(),
                    'payload' => $e->getPayload(),
                ]);
            }

            return $order;
        } catch (CourseFullException $e) {
            Log::error('Klarna order tried to book, but course if full', [
                'id' => $klarnaOrder['id'],
                'courseIds' => $courseIds,
                'pendingOrderId' => $pendingOrder->id
            ]);

            throw $e;
        }
    }

    /**
     * @param Klarna_Order $klarnaOrder
     * @param PendingExternalOrder $pendingOrder
     * @param FormRequest $request
     * @return mixed
     * @throws \Exception
     */
    private function confirmGiftCardOrder(Klarna_Order $klarnaOrder, PendingExternalOrder $pendingOrder, FormRequest $request)
    {
        try {
            $order = $this->orderService->storeGiftCardOrder($request, $pendingOrder->user, $pendingOrder->getGiftCardTypeId());

            try {
                // At this point make sure the order is created in your system and send a
                // confirmation email to the customer
                $klarnaOrder->acknowledge();
            } catch (Exception $e) {

                /** @var \Jakten\Services\Payment\Klarna\KlarnaService $klarnaService */
                $klarnaService = resolve(\Jakten\Services\Payment\Klarna\KlarnaService::class);
                $klarnaService->cancelOrder($order, true);

                Log::warning('Error of updating remote Klarna order: ' . $klarnaOrder['id'], [
                    'message' => $e->getMessage(),
                    'payload' => $e->getPayload(),
                ]);
            }

            return $order;
        } catch (\Exception $e) {
            Log::error('Klarna order tried to create gift card but failed', [
                'id' => $klarnaOrder['id'],
                'pendingOrderId' => $pendingOrder->id
            ]);

            throw $e;
        }
    }

    /**
     * @param Order $order
     * @param array $item
     * @return \Klarna\Rest\OrderManagement\Capture
     */
    public function captureOrderItem(Order $order, array $item)
    {
        $order = new \Klarna\Rest\OrderManagement\Order($this->connector, $order->external_order_id);
        return $order->createCapture([
            "captured_amount" => $item['total_amount'],
            "description" => $item['name'],
            "order_lines" => [
                $item
            ]
        ]);
    }

    /**
     * @param array $orderItems
     * @return array
     */
    public static function prepareKlarnaOrderLines($orderItems) {
        $items = [
            "captured_amount" => 0,
            "description" => '',
            "order_lines" => [],
        ];

        $orderItems->each(function ($orderItem) use (&$items) {
            $amount = $orderItem->amount * $orderItem->quantity;
            $items["captured_amount"] += (int)($amount * 100);
            $items["description"] .= $orderItem->name;
            $items["order_lines"][] = [
                'type'              => !$orderItem->provision ? 'discount' : 'digital',
                'reference'         => $orderItem->external_id,
                'name'              => $orderItem->name,
                'quantity'          => $orderItem->quantity,
                'unit_price'        => (int)($orderItem->amount * 100),
                'tax_rate'          => 2500,
                "total_amount"      => (int)($amount * 100),
                "total_tax_amount"  => (int)($amount * 100 * 0.2)
            ];
        });

        return $items;
    }

    /**
     * @param Order $order
     * @param array|OrderItem[] $orderItems
     *
     * @return string|bool
     *
     */
    public function captureOrderItems(Order $order, $orderItems = [], $bookingFee = [])
    {
        /** @var KKJTelegramBotService $bot */
        $bot = resolve(KKJTelegramBotService::class);
        Log::info("Initializing order items activation for Order #{$order->id}");

        $bot->log('items_to_activate', compact('orderItems'));

        if (!$orderItems instanceof Collection) {
            $orderItems = array_map(function ($itemId) {
                return is_int($itemId) ? $itemId : (int)$itemId;
            }, $orderItems);

            /** @var Collection $orderItems */
            $orderItems = OrderItem::query()
                ->whereIn('id', $orderItems)
                ->where('delivered', '=', 0)->where('cancelled', '=', 0)
                ->get();
        }

        try {
            $bot->log('order_item_activation_init', [
                'reservation_id' => $order->external_reservation_id
            ]);

            Log::info('Order Items : ' . json_encode($orderItems));
            Log::info("Reservation ID : {$order->external_reservation_id}");
            $orderKlarna = new \Klarna\Rest\OrderManagement\Order($this->connector, $order->external_order_id);

            $items = static::prepareKlarnaOrderLines($orderItems);
            if (count($bookingFee)) {
                foreach ($bookingFee as $extra) {
                    $founded = false;

                    if ($extra['name'] === 'Körkortsteori och Testprov') {//to fix theori course double cupture
                        continue;
                    }

                    if ($extra["name"] === config('klarna.gift_cart_name')) {
                        foreach ($items["order_lines"] as $key => $line) {
                            if ($line['reference'] === $extra['reference']) {
                                $items["order_lines"][$key] = $extra;
                                $founded = true;
                            }
                        }
                    }

                    if (!$founded) {
                        $items["order_lines"][] = $extra;
                        $items["captured_amount"] += $extra['total_amount'];
                    }
                }
            }

            $capture = null;

            try {
                $capture = $orderKlarna->createCapture($items);
            } catch (Exception $exception) {
                if (strpos($exception->getMessage(), '504 Gateway Time-out')) {
                    $bot->log("order_{$order->id}_reactivation_started");
                    $capture = $orderKlarna->createCapture($items);
                }
                $bot->log("order_{$order->id}_activation_failed", ['exception' => $exception->getMessage()]);
                Log::info("Error #{$order->id}");
                Log::info("Message #{$order->id} : " . $exception->getMessage());
            }

            if ($capture) {
                $orderItems->each(function ($orderItem) use ($capture) {
                    $orderItem->update([
                        'external_invoice_id' => $capture->getId(),
                        'delivered' => true
                    ]);
                });
                $order->handleOrderIfAllItemsAreFulfilled();
                $bot->log("order_{$order->id}_activation", ['finished' => true]);
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            $bot->log('order_items_activation_error', [
                'reservation_id' => $order->id,
                'message' => $exception->getMessage()
            ]);
            Log::info($exception->getMessage());
            return false;
        }
    }

    /**
     * @param Order $order
     * @param int $qty
     * @param string $externalId
     * @return \A
     * @throws KlarnaException
     */
    public function activateOrderByArtNo(Order $order, int $qty, string $externalId) {
        $klarna = $this->initKlarnaFromOrder($order);
        Log::info("Adding Art no. {$externalId}");

        /** @var KKJTelegramBotService $bot */
        $bot = resolve(KKJTelegramBotService::class);
        $bot->log("order_{$order->id}_adding_art_no", ['art_no' => $externalId]);
        $bot->log('order_item_activation', ['order_id' => $order->id, 'item' => $externalId]);

        $klarna->addArtNo($qty, $externalId);

        $response = $klarna->activate(
            $order->external_reservation_id,
            null,
            KlarnaFlags::RSRV_PRESERVE_RESERVATION
        );

        $bot->log('order_item_activation_klarna_response', compact('response'));

        return $response;
    }

    /**
     * @param Order $order
     * @param $orderItem
     * @throws KlarnaException
     */
    protected function activateItem(Order $order, $orderItem)
    {
        /** @var KKJTelegramBotService $bot */
        $bot = resolve(KKJTelegramBotService::class);

        $response = $this->activateOrderByArtNo($order, $orderItem->quantity, $orderItem->external_id);

        list($status, $invoiceId) = $response;

        switch ($status) {
            case 'ok':
                $orderItem->update([
                    'external_invoice_id' => $invoiceId,
                    'delivered' => true
                ]);
                $bot->log('order_item_activation_status', ['success' => true, 'order_id' => $order->id, 'item_id' => $orderItem->id]);
                break;

            default:
                Log::error('Failed to activate items', [
                    'order_id' => $orderItem->order_id,
                    'response' => $response
                ]);
                $bot->log('order_item_activation_status', ['success' => false, 'order_id' => $order->id, 'item_id' => $orderItem->id]);
                break;
        }
    }

    /**
     * @param Order $order
     * @param array|OrderItem[] $orderItems
     *
     * @return string|bool
     *
     */
    public function activateItems(Order $order, $orderItems = [])
    {
        /** @var KKJTelegramBotService $bot */
        $bot = resolve(KKJTelegramBotService::class);
        Log::info("Initializing order items activation for Order #{$order->id}");

        $bot->log('items_to_activate', compact('orderItems'));

        if (!$orderItems instanceof Collection) {
            $orderItems = array_map(function ($itemId) {
                return is_int($itemId) ? $itemId : (int)$itemId;
            }, $orderItems);

            /** @var Collection $orderItems */
            $orderItems = OrderItem::query()
                ->whereIn('id', $orderItems)
                ->get();
        }

        try {
            $bot->log('order_item_activation_init', [
                'reservation_id' => $order->external_reservation_id
            ]);

            Log::info('Order Items : ' . json_encode($orderItems));
            Log::info("Reservation ID : {$order->external_reservation_id}");

            $orderItems->each(function (OrderItem $orderItem) use ($order, $bot) {
                try {
                    self::activateItem($order, $orderItem);
                } catch (Exception $exception) {
                    if (strpos($exception->getMessage(), '504 Gateway Time-out')) {
                        $bot->log("order_{$order->id}_reactivation_started");
                        self::activateItem($order, $orderItem);
                    }
                    $bot->log("order_{$order->id}_activation_failed", ['exception' => $exception->getMessage()]);
                }
            });

            $order->handleOrderIfAllItemsAreFulfilled();

            $bot->log("order_{$order->id}_activation", ['finished' => true]);
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
            return $exception->getMessage();
        }
    }

    /**
     * @param Order $order
     * @param array|OrderItem[]|Collection $orderItems
     *
     * @return string
     *
     */
    public function refund(Order $order, $orderItems = [])
    {
        Log::info("Called refund for items : " . json_encode($orderItems, JSON_PRETTY_PRINT));

        $orderManager = new \Klarna\Rest\OrderManagement\Order($this->connector, $order->external_order_id);
        $itemsRefund = static::prepareKlarnaOrderLines($orderItems);
        $itemsRefund['refunded_amount'] = $itemsRefund['captured_amount'];

        /** @var KKJTelegramBotService $kkjBot */
        $kkjBot = resolve(KKJTelegramBotService::class);

        try {
            $orderManager->refund($itemsRefund);
            $kkjBot->log('refund_klarna_from_old_order_success', [compact('order'), compact(Auth::user())]);
        } catch (ConnectorException $e) {
            if ($e->getErrorCode() !== 'REFUND_NOT_ALLOWED') {
                Log::error('Error when refund Klarna order', [
                    'message' => $e->getMessage()
                ]);
                throw $e;
            }

            $kkjBot->log('refund_klarna_from_old_order_failed', [compact('order'), compact(Auth::user())]);
        }

        $items = [
            'total' => $order->items()->count(),
            'cancelledItems' => $order->items()->where('cancelled', true)->count()
        ];

        if ($items['total'] === $items['cancelledItems']) {
            $order->update([
                'cancelled' => true
            ]);
        }
    }

    /**
     * @param Order $order
     * @return bool
     * @throws \KlarnaException
     */
    public function cancelOrder(Order $order, $total = false)
    {

        $klarnaOrder = $this->getOrder(null, $order->external_order_id);

        foreach ($klarnaOrder['order_lines'] as $item) {
            if ($item['name'] == config('fees.booking_fee_to_kkj_name')) {
                $feeItem = new \stdClass();
                $feeItem->type = 'digital';
                $feeItem->external_id = $item['reference'];
                $feeItem->name = $item['name'];
                $feeItem->quantity = 1;
                $feeItem->amount = config('fees.booking_fee_to_kkj');

                $feeItemCollection = new Collection();
                $feeItemCollection->push($feeItem);

                break;
            }
        }

        $orderItems = $order->items()->get();

        if ($total && $orderItems) {
            $orderItems->push($feeItem);
        }

        $this->refund($order, $orderItems);

        if ($total) {
            return !!$klarnaOrder->cancel();
        }

        $items = $this::prepareKlarnaOrderLines($feeItemCollection);
        $items['order_amount'] = $items['captured_amount'];

        try {
            return !!$klarnaOrder->updateAuthorization($items);
        } catch (ConnectorException $e) {
            if ($e->getErrorCode() !== 'NOT_ALLOWED') {
                Log::error('Error when refund Klarna order', [
                    'message' => $e->getMessage()
                ]);
                throw $e;
            }

            return true;
        }
    }

    /**
     * @param $paymentId
     * @param $paymentSecret
     * @param $reservationId
     * @throws \KlarnaException
     */
    public function cancelReservation($paymentId, $paymentSecret, $reservationId)
    {
        $klarna = $this->initKlarna($paymentId, $paymentSecret);
        $klarna->cancelReservation($reservationId);
    }

    /**
     * @param Order $order
     * @param array $orderItems
     * @throws \KlarnaException
     */
    public function updateReservation(Order $order, array $orderItems)
    {
        $klarna = $this->initKlarnaFromOrder($order);
        foreach ($orderItems as $orderItem) {
            $id = str_random();
            $klarna->addArticle(
                $orderItem->quantity,
                $id,
                $orderItem->name,
                $orderItem->amount,
                25 //VAT
            );
        }

        $klarna->setEstoreInfo($order->id);

        $klarna->update($order->external_reservation_id);
    }

    /**
     * @param Order $order
     * @return \DateTime
     * @throws KlarnaException
     */
    public function updateOrderExpirationDate(Order $order)
    {
        if ($order->isKlarnaV3()) {
            $klarna = new \Klarna\Rest\OrderManagement\Order($this->connector, $order->external_order_id);
            $klarna->extendAuthorizationTime()->fetch();

            return $klarna['expires_at'];
        } else {
            $klarna = $this->initKlarnaFromOrder($order);
            return $klarna->extendExpiryDate((string)$order->external_reservation_id);
        }
    }

    /**
     * @param Order $order
     * @return \Klarna\Rest\Checkout\Order|\Klarna
     * @throws \KlarnaException
     */
    private function initKlarnaFromOrder(Order $order, $checkout = false)
    {
        if ($order->isKlarnaV3()) {
            return $checkout ? new \Klarna\Rest\Checkout\Order($this->connector) : new \Klarna\Rest\OrderManagement\Order($this->connector, $order->external_order_id);
        }

        if (!$order->booking_fee) {
            /** @var KKJTelegramBotService $kkjBot */
            $kkjBot = resolve(KKJTelegramBotService::class);
            $kkjBot->log('activate_klarna_from_old_order', compact('order'));

            $org = $order->school->organization;
            return $this->initKlarna((int)$org->payment_id, $org->payment_secret);
        }

        return $this->initKlarna((int)config('klarna.kkj_payment_id'), config('klarna.kkj_payment_secret'));
    }

    /**
     * @param $paymentId
     * @param $paymentSecret
     * @return \Klarna
     * @throws \KlarnaException
     */
    private function initKlarna($paymentId, $paymentSecret)
    {
        $klarna = new \Klarna();
        $klarna->config(
            $paymentId,
            $paymentSecret,
            \KlarnaCountry::SE,
            \KlarnaLanguage::SV,
            \KlarnaCurrency::SEK,
            config('app.env') === 'production' ? \Klarna::LIVE : \Klarna::BETA
        );

        return $klarna;
    }

    /**
     * @param KlarnaOnboardingResponse $klarnaResponse
     */
    public function setOnboarding(KlarnaOnboardingResponse $klarnaResponse)
    {
        $organization = $this->organizations->query()->where('external_sign_up_id', $klarnaResponse->id)->firstOrFail();
        if ($klarnaResponse->succeeded()) {
            $organization->payment_id = $klarnaResponse->merchant_id;
            $organization->payment_secret = $klarnaResponse->shared_secret;
            $organization->sign_up_status = KlarnaSignup::STATUS_SUCCESS;
            $organization->sign_up_status_text = KlarnaSignup::textForStatus(KlarnaSignup::STATUS_SUCCESS);
        } else {
            $organization->sign_up_status = KlarnaSignup::STATUS_REJECTED;
            $organization->sign_up_status_text = KlarnaSignup::textForStatus(KlarnaSignup::STATUS_REJECTED);
            $organization->sign_up_rejected_reason = $klarnaResponse->rejection_reason;
        }

        $organization->save();
    }

    /**
     * @param $resourceUrl
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handleOnboardingResponse($resourceUrl)
    {
        $client = new Client();
        $response = $client->request(
            'POST',
            $resourceUrl,
            [
                'auth' => [
                    config('klarna.kkj_native_onboarding_username'),
                    config('klarna.kkj_native_onboarding_payment_secret')
                ]
            ]
        );

        $response = $response->getBody()->getContents();
        $decoded = json_decode($response, true);

        Log::debug('Handle onboarding response', [
            'data' => $response,
            'decoded' => $decoded
        ]);

        $klarnaResponse = new KlarnaOnboardingResponse($decoded);
        $this->setOnboarding($klarnaResponse);
    }

    /**
     * @param $data*
     */
    public function updateOnboarding($data)
    {
        $organization = $this->organizations->query()->where('external_sign_up_id', $data['signup_id'])->firstOrFail();
        $organization->sign_up_status = $data['status'];
        $organization->sign_up_status_text = KlarnaSignup::textForStatus($organization->sign_up_status);
        $organization->save();
    }

    /**
     * @param Organization $organization
     * @return bool|KlarnaError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function nativeOnboarding(Organization $organization)
    {
        switch ($organization->sign_up_status) {
            case KlarnaSignup::STATUS_NOT_INITIATED:
                return $this->initiateKlarnaOnboarding($organization);
                break;

            default:
                // code...
                break;
        }
    }

    /**
     * Sends initial onboarding signup request to Klarna.
     * @param Organization $organization
     * @return bool|KlarnaError////
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function initiateKlarnaOnboarding(Organization $organization)
    {
        $onboarding = new KlarnaNativeOnboarding($organization);
        $client = new Client();

        try {
            $response = $client->request(
                'POST',
                config('klarna.kkj_native_onboarding_url'),
                [
                    'auth' => [
                        config('klarna.kkj_native_onboarding_username'),
                        config('klarna.kkj_native_onboarding_payment_secret')
                    ],
                    'json' => $onboarding->data,
                ]
            );

        } catch (\Exception $e) {
            $error = new KlarnaError($e, $onboarding->data);
            //$annotation = $this->annotationService->create('', AnnotationType::KLARNA_ONBOARDING_ERROR, $error->get());
            //$organization->comments()->save($annotation);
            Log::error(AnnotationType::KLARNA_ONBOARDING_ERROR, $error->get());
            return $error;
        }

        $response = json_decode($response->getBody()->getContents());
        $organization->external_sign_up_id = $response->signup_id;
        $organization->sign_up_status = KlarnaSignup::STATUS_WAITING;
        $organization->sign_up_status_text = KlarnaSignup::textForStatus(KlarnaSignup::STATUS_WAITING);
        $organization->save();

        Log::debug('Onboarding request sent', [
            'data' => $onboarding->data,
        ]);

        Log::debug('Onboarding request response', [
            'response' => $response,
        ]);

        return true;
    }
}
