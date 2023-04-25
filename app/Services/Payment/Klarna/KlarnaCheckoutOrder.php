<?php namespace Jakten\Services\Payment\Klarna;

use Illuminate\Support\{Collection, Facades\URL};
use Jakten\Models\{Course, Organization, School, User};

/**
 * Class KlarnaCheckoutOrder
 * @package Jakten\Services\Payment\Klarna
 */
class KlarnaCheckoutOrder
{
    /**
     * @const KLARNA_ORDER_ID_PLACEHOLDER
     */
    const KLARNA_ORDER_ID_PLACEHOLDER = 'klarnaOrderIdPlaceholder';

    /**
     * @var $data
     */
    public $data;

    /**
     * @var $merchantId
     */
    private $merchantId;

    /**
     * @var $courses
     */
    private $courses;

    /**
     * @var $addons
     */
    private $addons;

    /**
     * @var School
     */
    private $addonSchool;

    /**
     * @var $customAddons
     */
    private $customAddons;

    /**
     * @var \Jakten\Models\School
     */
    private $school;

    /**
     * @var $orderAmount
     */
    private $orderAmount = 0;

    /**
     * @var $orderTaxAmount
     */
    private $orderTaxAmount = 0;

    /**
     * @var array $klarnaCartItems
     */
    private $klarnaCartItems = [];

    /**
     * @var User
     */
    private $user;

    /**
     * @return string KLARNA_KKJ_PAYMENT_ID
     */
    public static function getKkjPaymentId()
    {
        return env('KLARNA_V3_KKJ_PAYMENT_ID');
    }

    /**
     * @return string KLARNA_KKJ_PAYMENT_SECRET
     */
    public static function getKkjPaymentSecret()
    {
        return env('KLARNA_V3_KKJ_PAYMENT_ID');
    }

    /**
     * KlarnaCheckoutOrder constructor.
     * @param $merchantId
     */
    public function __construct($merchantId)
    {
        $this->merchantId = $merchantId;
    }

    public function setSchoolOrderData(School $school = null, Collection $courses = null, array $addons = null, array $customAddons = null)
    {
        $this->school = $school;
        $this->courses = $courses;
        $this->addons = $addons;
        $this->customAddons = $customAddons;

        if ($this->school) {
            $this->addonSchool = $this->school;
            return;
        }

        if($this->addons && count($this->addons[0])) {

            if (isset($this->addons[0]['pivot'])) {
                $this->addonSchool =  School::whereId($this->addons[0]['pivot']['school_id'])->first();
            }

            if (isset($this->addons[0]['school_id'])) {
                $this->addonSchool = School::whereId($this->addons[0]['school_id'])->first();
            }

        }

        if($this->customAddons && count($this->customAddons[0])) {
            if (isset($this->customAddons[0]['pivot'])) {
                $this->addonSchool =  School::whereId($this->customAddons[0]['pivot']['school_id'])->first();
            }

            if (isset($this->customAddons[0]['school_id'])) {
                $this->addonSchool = School::whereId($this->customAddons[0]['school_id'])->first();
            }
        }
    }

    public function setBillingAddressData($user = null)
    {
        $this->user = $user;
    }

    public function getSubMerchantId() {
        if ($this->school) {
            return $this->school->id;
        }

        if($this->courses && $this->courses->first()) {
            return $this->courses->first()->school->id;
        }

        if($this->addonSchool) {
            return $this->addonSchool->id;
        }

        return null;
    }

    public function getMerchantRegistrationDate() {
        $EMD_FORMAT = 'Y-m-d\TH:m:s';

        if ($this->school) {
            return $this->school->created_at->format($EMD_FORMAT);
        }

        if($this->courses && $this->courses->first()) {
            return $this->courses->first()->school->created_at->format($EMD_FORMAT);
        }

        if($this->addonSchool) {
            return $this->addonSchool->created_at->format($EMD_FORMAT);
        }

        return null;
    }

    public function getMerchantUpdateDate() {
        $EMD_FORMAT = 'Y-m-d\TH:m:s';

        if ($this->school) {
            return $this->school->updated_at->format($EMD_FORMAT);
        }

        if($this->courses && $this->courses->first()) {
            return $this->courses->first()->school->updated_at->format($EMD_FORMAT);
        }

        if($this->addonSchool) {
            return $this->addonSchool->updated_at->format($EMD_FORMAT);
        }

        return null;
    }

    public function getData()
    {
        $merchant = $this->school ? $this->buildMerchant() : $this->buildGiftCardMerchant();
        $billingAddress = $this->user ? $this->buildBillingAddress() : null;
        $orderLines = $this->getCartItemData();

        $sellerObject = [];
        if ($this->school) {
            $sellerObject['email'] = $this->school->contact_email;
            $sellerObject['pno']   = (string)$this->school->id;
        } else {
            $sellerObject['email'] = $this->courses && $this->courses->first() ? $this->courses->first()->school->contact_email : '';
            $sellerObject['pno']   = $this->courses && $this->courses->first() ? (string)$this->courses->first()->school->id : '';
        }

        $emd = [
            'voucher' => $this->getVouchers(),
            'event' => $this->getEvents(),
        ];

        if ($this->getSubMerchantId()) {
            $accountLastModified = new \stdClass();
            $accountLastModified->listing = $this->getMerchantUpdateDate();

            $emd['marketplace_seller_info'] = [
                    [
                    'unique_account_identifier_seller'   => $sellerObject,
                    'sub_merchant_id'   => (string)$this->getSubMerchantId(),
                    'product_category'  => 'Driving courses',
                    'product_name'  => $this->getProductsNames(),
                    'product_price'  => $this->orderAmount,
                    'account_registration_date'  => $this->getMerchantRegistrationDate(),
                    'account_last_modified'  => $accountLastModified,
                ]
            ];
        } else {
            $emd['marketplace_seller_info'] = [];
        }

        if ($this->user) {
            $emd['customer_account_info'][] = $this->buildCustomerAccountInfo();
            $emd['payment_history_simple'][] = [
                "unique_account_identifier" => (string)$this->user->id,
                "paid_before" => true
            ];
        } else {
            session_id(str_random());
            $emd['payment_history_simple'][] = [
                "unique_account_identifier" => (string)session_id(),
                "paid_before" => false
            ];
        }

        return [
            'purchase_country'  => 'SE',
            'purchase_currency' => 'SEK',
            'locale'            => 'sv-se',
            'merchant_urls'     => $merchant,
            'order_lines'       => $orderLines,
            'order_amount'      => $this->orderAmount,
            'order_tax_amount'  => $this->orderTaxAmount,
            'gui' => [
                'options' => ['disable_autofocus'],
            ],
            'attachment' => [
                'content_type' => 'application/vnd.klarna.internal.emd-v2+json',
                'body' => json_encode($emd),
            ],
            'billing_address' => $billingAddress,
        ];
    }

    public function getCourseId()
    {
        return $this->courses->first() ? $this->courses->first()->id : '';
    }

    private function getConfirmationUri()
    {
        $routeParams = [];
        if ($this->school) {
            $routeParams['schoolSlug'] = $this->school->slug;
        }

        return static::prepareKlarnaUri(route('shared::payment.confirmed', $routeParams));
    }

    private function getCheckOutUri()
    {
        $checkOutUri = null;
        if (!$this->school) {
            $checkOutUri = route('shared::gift_card.index');
        } else {
            $checkOutUri = route('shared::courses.show', [
                'citySlug' => $this->school->city->slug,
                'schoolSlug' => $this->school->slug,
                'courseId' => $this->getCourseId()
            ]);
        }

        return $checkOutUri;
    }

    private function getPushUri()
    {
        $routeParams = [];
        $pushUri = null;
        if ($this->school) {
            $routeParams['schoolId'] = $this->school->id;
        }

        return static::prepareKlarnaUri(route('public::klarna.checkout.push', $routeParams));
    }

    /**
     * Need to use this to replace klarnaOrderIdPlaceholder
     * (placeholder argument required when using route() to retrieve the endpoint)
     * @param $uri
     * @return mixed
     */
    public static function prepareKlarnaUri($uri)
    {
        return $uri . '?klarna_order_id={checkout.order.id}';
    }

    public function buildMerchant()
    {
        return [
            'id' => $this->merchantId,
            'terms' => URL::to('/kopvillkor'),
            'checkout' => $this->getCheckOutUri(),
            'confirmation' => $this->getConfirmationUri(),
            'push' => $this->getPushUri(),
        ];
    }

    public function buildGiftCardMerchant()
    {
        return [
            'id' => $this->merchantId,
            'terms' => URL::to('/kopvillkor'),
            'checkout' => $this->getCheckOutUri(),
            'confirmation' => $this->getConfirmationUri(),
            'push' => $this->getPushUri(),
        ];
    }

    public function buildCustomerAccountInfo()
    {
        $EMD_FORMAT = 'Y-m-d\TH:m:s';

        return !$this->user ? [] : [
            'unique_account_identifier' => $this->user->given_name .' '. $this->user->family_name,
            'account_registration_date' => $this->user->created_at->format($EMD_FORMAT),
            'account_last_modified' => $this->user->updated_at->format($EMD_FORMAT),
        ];
    }

    public function buildBillingAddress()
    {
        return [
            'email' => $this->user->email
        ];
    }

    public function addCartItem(KlarnaCartItem $item)
    {
        $this->klarnaCartItems[] = $item;
    }

    private function getProductsNames()
    {
        $nameString = [];
        foreach ($this->klarnaCartItems as $klarnaCartItem) {
            /** @var KlarnaCartItem $klarnaCartItem */
            $nameString[] = $klarnaCartItem->data['name'];
        }
        return implode(', ', $nameString);
    }

    private function getCartItemData()
    {
        $cartItemData = [];
        foreach ($this->klarnaCartItems as $klarnaCartItem) {
            /** @var KlarnaCartItem $klarnaCartItem */
            $cartItemData[] = $klarnaCartItem->data;
            $this->orderAmount += $klarnaCartItem->data['total_amount'];
            $this->orderTaxAmount += $klarnaCartItem->data['total_tax_amount'];
        }

        return $cartItemData;
    }

    private function getVouchers()
    {
        if (!$this->school) {
            return [];
        }

        $vouchers = [];
        $EMD_FORMAT = 'Y-m-d\TH:m:s';

        foreach ($this->courses as $course) {
            /** @var Course $course */
            $vouchers[] = [
                'voucher_name' => $course->name,
                'voucher_company' => $course->school->name,
                'start_time' => $course->start_time->format($EMD_FORMAT),
                'end_time' => $course->start_time->addMinutes($course->length_minutes)->format($EMD_FORMAT),
            ];
        }

        return $vouchers;
    }

    private function getEvents()
    {
        if (!$this->school) {
            return [];
        }

        $events = [];
        $EMD_FORMAT = 'Y-m-d\TH:m:s';

        foreach ($this->courses as $course) {
            /** @var Course $course */
            $events[] = [
                'event_name' => $course->name,
                'event_company' => $course->school->name,
                'start_time' => $course->start_time->format($EMD_FORMAT),
                'end_time' => $course->start_time->addMinutes($course->length_minutes)->format($EMD_FORMAT),
            ];
        }

        return $events;
    }
}
