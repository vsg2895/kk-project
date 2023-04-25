<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderItem
 *
 * @property int id
 * @property string external_id
 * @property string external_invoice_id
 * @property int order_id
 * @property Order order
 * @property int school_id
 * @property School school
 * @property int|null course_id
 * @property Course|null course
 * @property int|null custom_addon_id
 * @property CustomAddon|null custom_addon
 * @property float amount
 * @property int quantity
 * @property string type
 * @property int provision
 * @property string comment
 * @property CourseParticipant participant
 * @property bool delivered
 * @property bool credited
 * @property string name
 * @property GiftCard giftCard
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class OrderItem extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'order_items';

    /**
     * @var array $fillable
     */
    protected $fillable = ['school_id', 'order_id', 'course_id', 'custom_addon_id',
        'amount', 'quantity', 'type', 'provision', 'comment', 'delivered', 'external_id',
        'external_invoice_id', 'credited', 'cancelled', 'created_at', 'gift_card_id', 'benefit_id'
    ];

    /**
     * @var array $appends
     */
    protected $appends = ['name', 'is_cancellable'];

    /**
     * @var array $hidden
     */
    protected $hidden = ['order', 'school'];

    protected $casts = [
        'delivered' => 'bool',
        'cancelled' => 'bool',
        'credited' => 'bool'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return mixed
     */
    public function school()
    {
        return $this->belongsTo(School::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function course()
    {
        return $this->belongsTo(Course::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class);
    }

    /**
     * @return mixed
     */
    public function customAddon()
    {
        return $this->belongsTo(CustomAddon::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function participant()
    {
        return $this->hasOne(CourseParticipant::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function packageParticipant()
    {
        return $this->hasOne(AddonParticipant::class);
    }

    public function benefit()
    {
        return $this->belongsTo(Benefit::class);
    }

    /**
     * @return bool
     */
    public function isCourse()
    {
        return (bool)$this->course_id;
    }

    /**
     * @return bool
     */
    public function isCustomAddon()
    {
        return (bool)$this->custom_addon_id;
    }

    /**
     * @return bool
     */
    public function isGiftCard()
    {
        return !!$this->giftCard;
    }

    /**
     * @param $value
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    public function getNameAttribute($value)
    {
        if ($this->isGiftCard()) {
            return $this->giftCard->title;
        }

        if ($this->isCourse()) {
            $regex = '/RISK_(ONE|TWO)_(MC|CAR)/';

            if (preg_match($regex, $this->type, $typesMatch)) {
                $type = [
                    'CAR' => 'B',
                    'MC' => 'A'
                ];

                return trans('vehicle_segments.' . strtolower($this->type)) . " {$type[$typesMatch[2]]}";
            }

            return trans('vehicle_segments.' . strtolower($this->type));
        }

        return $this->type;
    }

    /**
     * @return bool
     */
    public function getIsCancellableAttribute()
    {
        $cancellable = true;

        if ($this->isCourse()) {
            $tooLate = Carbon::now()->addHours(24)->greaterThan($this->course->start_time);
            if ($tooLate) {
                $cancellable = false;
            }
        }

        if ($this->isCustomAddon()) {
            $tooLate = Carbon::now()->subDays(14)->greaterThan($this->updated_at);
            if ($tooLate) {
                $cancellable = false;
            }
        }

        return $cancellable;
    }

    /**
     * @return bool
     */
    public function cancel()
    {
        return $this->update(['cancelled' => true, 'credited' => true]);
    }
}
