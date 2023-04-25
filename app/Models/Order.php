<?php namespace Jakten\Models;

use Carbon\Carbon;
use Jakten\Facades\Auth;
use Illuminate\Database\Eloquent\{Collection, Model};
use Jakten\Services\StudentLoyaltyProgramService;

/**
 * Class Order
 *
 * @property int id
 * @property string external_order_id
 * @property string external_reservation_id
 * @property int school_id
 * @property School school
 * @property int user_id
 * @property User user
 * @property int invoice_id
 * @property Invoice invoice
 * @property string payment_method
 * @property bool paid
 * @property OrderItem[]|Collection items
 * @property bool cancelled
 * @property bool rebooked
 * @property bool handled
 * @property bool invoice_sent
 * @property Carbon paid_at
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property bool all_items_cancelled
 */
class Order extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'orders';

    /**
     * @var array $fillable
     */
    protected $fillable = ['user_id', 'payment_method', 'paid_at', 'invoice_id', 'booking_fee', 'klarna_expires_at',
        'external_order_id', 'school_id', 'external_reservation_id', 'cancelled', 'handled', 'rebooked', 'invoice_sent',
        'created_at', 'saldo_used'];

    /**
     * @var array $with
     */
    protected $with = ['items.benefit', 'user'];

    /**
     * @var array $dates
     */
    protected $dates = ['created_at', 'updated_at', 'paid_at', 'klarna_expires_at'];

    /**
     * @var array $appends
     */
    protected $appends = ['invoice_amount', 'paid', 'can_be_cancelled', 'order_value', 'title', 'status_text', 'user_email_or_name', 'all_items_cancelled'];

    /**
     * @return mixed
     */
    public function school()
    {
        return $this->belongsTo(School::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|User
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|OrderItem[]
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class)->orderBy('gift_card_id', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function benefits()
    {
        return $this->hasMany(Benefit::class);
    }

    /**
     * @return bool
     */
    public function isGiftCardOrder()
    {
        return ($this->items->count() === 1) && $this->items->first()->isGiftCard();
    }

    /**
     * @return float|int
     */
    public function giftCardBalanceUsed()
    {
        if ($this->isGiftCardOrder()) {
            return 0;
        } else {
            return abs($this->items->filter(function (OrderItem $item, $key) {
                return $item->isGiftCard();
            })->map(function ($giftCardItem) {
                return $giftCardItem->amount;
            })->sum());
        }
    }

    public function discountBenefitUsed()
    {
        $discountAmount = 0;

        foreach ($this->items as $item) {
            if ($item->benefit && $item->benefit->benefit_type === StudentLoyaltyProgramService::BENEFIT_TYPES['discount']) {
                $discountAmount += $item->amount * $item->benefit->amount / 100;//todo check quantity here and in payment
            }
        }

        return $discountAmount;
    }

    /**
     * @return OrderItem | null
     */
    public function getFirstOrderItem()
    {
        return $this->items->first();
    }

    /**
     * @return bool
     */
    public function getFirstCourse()
    {
        $first = $this->items->where('course_id', '!=', null)->first();

        return $first ? ($first->course->part ? $first->course->name . ' ' . trans('courses.part') . ' ' . $first->course->part : $first->course->name)  : false;
    }

    /**
     * @return string
     */
    public function getTitleAttribute()
    {
        return $this->getFirstOrderItem() ? $this->getFirstOrderItem()->name : '';
    }

    /**
     * @return null|string
     */
    public function getStatusTextAttribute()
    {
        $statusText = null;
        if ($this->items->count() > 0 && $this->getFirstOrderItem()->isGiftCard()) {
            $giftCard = $this->getFirstOrderItem()->giftCard;
            if ($giftCard->claimed) {
                $statusText = "Använt av ";

                if ($giftCard->claimer_id == Auth::user()->id) {
                    $statusText .= "dig, " . $giftCard->remaining_balance . " kr återstår";
                } else {
                    $statusText .= $giftCard->claimer->name;
                }
            } else {
                $statusText = "Ej använt";
            }
        } else if ($this->cancelled) { //Process all other order types below
            $statusText = "Avbokad";
        } else if ($this->handled) {
            $statusText = "Levererad";
        } else {
            $statusText = "Kommande";
        }

        return $statusText;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * @param $value
     * @return float|int
     */
    public function getInvoiceAmountAttribute($value)
    {
        $total = 0;

        foreach ($this->items as $item) {
            if ($item->delivered && !$item->credited) {
                $total += (($item->amount * $item->quantity) * $item->provision) / 100;
            }
        }

        return $total;
    }

    /**
     * @param $value
     * @return bool
     */
    public function getPaidAttribute($value)
    {
        return !is_null($this->paid_at);
    }

    /**
     * @return float|int
     */
    public function getOrderValueAttribute()
    {
        $total = 0;

        foreach ($this->items as $item) {
            if (!$item->cancelled) {
                $total += $item->amount * $item->quantity;
            }
        }

        return $this->cancelled ? $total : $total + $this->saldo_amount - $this->discountBenefitUsed();
    }

    /**
     * @return float|int
     */
    public function getSchoolOrderValueAttribute()
    {
        $total = 0;

        foreach ($this->items as $item) {
            if (!$item->cancelled) {
                $total += $item->amount * $item->quantity;
            }
        }

        return $total;
    }

    /**
     * @return bool
     */
    public function includesGiftCard()
    {
        foreach ($this->items as $item) {
            if ($item->isGiftCard()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canBeCancelled()
    {
        $canBeCancelled = true;

        if ($this->cancelled) {
            return false;
        }

        if ($this->includesGiftCard() && $this->items->count() === 1) {
            return false;
        }

        foreach ($this->items as $orderItem) {
            if ($orderItem->external_invoice_id) {
                $canBeCancelled = false;
            }

            if ($orderItem->isCourse()) {
                $tooLate = Carbon::now()->addHours(48)->greaterThan($orderItem->course->start_time);
                if ($tooLate || $orderItem->course->vehicle_segment_id === VehicleSegment::ONLINE_LICENSE_THEORY) {
                    $canBeCancelled = false;
                }
            }

            if (!$orderItem->isCourse() && !$orderItem->isGiftCard()) {
                $canBeCancelled = false;
            }
        }

        return $canBeCancelled;
    }

    /**
     * @param $value
     * @return bool
     */
    public function getCanBeCancelledAttribute($value)
    {
        return $this->canBeCancelled();
    }

    /**
     * @return string
     */
    public function getUserEmailOrNameAttribute()
    {
        $user = $this->user;

        if ($user instanceof User) {
            return $user->name ?: $user->email;
        }

        return '-';
    }

    /**
     * @return bool
     */
    public function handleOrderIfAllItemsAreFulfilled()
    {
        $items = $this->items();
        $itemsCount = $items->count();
        $deliveredItemsCount = $items
            ->where('delivered', true)
            ->count();

        $this->update([
            'handled' => $itemsCount === $deliveredItemsCount
        ]);

        return $itemsCount === $deliveredItemsCount;
    }

    /**
     * @return bool
     */
    public function isPackageOrder()
    {
        $hasNonVehicleSegmentTypes = false;
        $segments = VehicleSegment::query()
            ->pluck('name')
            ->toArray();

        $this->items()
            ->each(function (OrderItem $orderItem) use (&$hasNonVehicleSegmentTypes, $segments) {
                if (!in_array($orderItem->type, $segments)) {
                    $hasNonVehicleSegmentTypes = true;
                }
            });

        return $hasNonVehicleSegmentTypes;
    }

    /**
     * @return bool
     */
    public function isKlarnaV3(){
        return substr_count($this->external_order_id, '-') > 0;
    }

    /**
     * @return bool
     */
    public function getAllItemsCancelledAttribute()
    {
        return $this->items()->count() === $this->items()->where('credited', true)->count();
    }

    /**
     * @return bool
     */
    public function cancel()
    {
        return $this->update([
            'cancelled' => true,
            'handled' => true
        ]);
    }

    /**
     * @return bool
     */
    public function rebook()
    {
        return $this->update([
            'cancelled' => true,
            'handled' => true,
            'rebooked' => true,
        ]);
    }

    /**
     * @return bool
     */
    public function shouldBeActivatedNow(): bool
    {
        $courses = Course::query()
            ->whereIn('id', $this->items->pluck('course_id'))
            ->get();

        $needToBeActivatedNow = false;

        $courses->each(function (Course $course) use (&$needToBeActivatedNow) {
            if ($course->start_time->subDay()->isPast()) {
                $needToBeActivatedNow = true;
            }
        });

        return $needToBeActivatedNow;
    }
}
