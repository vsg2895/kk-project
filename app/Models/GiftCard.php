<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Jakten\Facades\Auth;

/**
 * Class GiftCard
 * @package Jakten\Models
 * @property integer gift_card_type_id
 * @property string token
 * @property string title
 * @property float remaining_balance
 * @property integer buyer_id
 * @property integer claimer_id
 * @property bool claimed
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon expires
 * @property bool expired
 * @property bool reusable
 */
class GiftCard extends Model
{
    /**
     * @var array $fillable
     */
    protected $fillable = ['gift_card_type_id', 'token', 'remaining_balance', 'buyer_id', 'claimer_id', 'claimed', 'expires', 'reusable'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array $hidden
     */
    protected $hidden = ['token'];

    /**
     * @var array $appends
     */
    protected $appends = ['title', 'expired', 'remainingBalanceIfNotExpired'];

    /**
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'expires'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function giftCardType()
    {
        return $this->belongsTo(GiftCardType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function claimer()
    {
        return $this->belongsTo(User::class, 'claimer_id');
    }

    /**
     * @param $originalPrice
     * @return float|int
     */
    public function getNewPrice($originalPrice)
    {
        $newPrice = $originalPrice - ($this->remaining_balance * 100);

        if ($newPrice <= 0) {
            //If our gift card had more value than the original price then we need to set the new remaining balance. (not saved)
            $this->remaining_balance = (abs($newPrice) / 100);
            $newPrice = 0;
        } else {
            $this->remaining_balance = 0;
        }

        return $newPrice;
    }

    /**
     * @return bool
     */
    public function hasRemainingBalance()
    {
        return $this->remaining_balance && $this->remaining_balance > 0;
    }

    /**
     *
     * Gift Card claiming Process
     *
     * @param null $userId
     * @return bool
     */
    public function claim($userId = null)
    {
        $this->claimer_id = $userId ?? Auth::id();
        $this->claimed = true;

        return $this->save();
    }

    /**
     * @return string
     */
    public function getTitleAttribute()
    {
        $data = GiftCardType::getDataById($this->gift_card_type_id);
        return $data['name'] . " - " . $this->token . " " . $data['value'] . (in_array($this->gift_card_type_id, GiftCardType::PERCENT_TYPES) ? "%" : " kr");
    }

    /**
     * @return bool
     */
    public function getExpiredAttribute()
    {
        return $this->expires->isPast();
    }

    /**
     * @return float|int
     */
    public function getRemainingBalanceIfNotExpiredAttribute()
    {
        return $this->getExpiredAttribute() ?
            0 : $this->remaining_balance;
    }

    /**
     * Updates current balance of the gift card
     *
     * @param float $amount
     * @return bool
     */
    public function updateBalance(float $amount = 0.00)
    {
        return $this->update(['remaining_balance' => $this->remaining_balance + $amount]);
    }
}
