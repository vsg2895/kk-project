<?php namespace Jakten\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Jakten\Helpers\Roles;
use Jakten\Mail\ResetPassword;
use Jakten\Helpers\PhoneNumber;
use Illuminate\Notifications\Notifiable;
use Jakten\Services\StudentLoyaltyProgramService;
use Illuminate\Support\Facades\{Log, Mail};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\{Collection, ModelNotFoundException, SoftDeletes};
use Jakten\Services\KKJTelegramBotService;

/**
 * Class User
 *
 * @property int id
 * @property string given_name
 * @property string family_name
 * @property string name
 * @property string email
 * @property string password
 * @property string phone_number
 * @property int role_id
 * @property Organization organization
 * @property int organization_id
 * @property bool confirmed
 * @property SchoolRating[]|Collection ratings
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;

    /**
     * @var string $table
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array $fillable
     */
    protected $fillable = ['given_name', 'family_name', 'email', 'password', 'organization_id', 'phone_number', 'role_id', 'amount'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array $hidden
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * @var array $appends
     */
    protected $appends = ['name', 'gift_card_balance', 'gift_cards', 'theory_online_discount'];

    /**
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'expires'];

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (User $model) {
            $model->password = uniqid();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {
        if ($this->isOrganizationUser()) {
            return $this->belongsTo(Organization::class);
        }

        //Prevent the IDE-helper from crashing when generating models
        if (!\App::runningInConsole()) {
            throw new ModelNotFoundException('This user type does not belong to an organization');
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings()
    {
        return $this->hasMany(SchoolRating::class)
            ->where('verified', '=', 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class)
            ->where('cancelled', '=', 0);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function giftCardsClaimed()
    {
        return $this->hasMany(GiftCard::class, 'claimer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function giftCardsBought()
    {
        return $this->hasMany(GiftCard::class, 'buyer_id');
    }

    public function discountBenefits()
    {
        return $this->hasMany(Benefit::class)->where('user_id', $this->id)->where('claimed', false)
            ->where('benefit_type', StudentLoyaltyProgramService::BENEFIT_TYPES['discount']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return bool
     */
    public function isStudent()
    {
        return $this->role_id === Roles::ROLE_STUDENT;
    }

    /**
     * @return bool
     */
    public function isOrganizationUser()
    {
        return $this->role_id === Roles::ROLE_ORGANIZATION_USER;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role_id === Roles::ROLE_ADMIN;
    }

    /**
     * @return string
     */
    public function getRoleName()
    {
        return Roles::$roles[$this->role_id];
    }

    /**
     * Encrypt the password before saving
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * @param $value
     */
    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['phone_number'] = PhoneNumber::parsePhoneNumber($value);
    }

    /**
     * Get concatenated name
     *
     * @return string
     */
    public function getNameAttribute()
    {
        if (!$this->given_name && !$this->family_name) {
            return null;
        }
        return $this->given_name . ' ' . $this->family_name;
    }

    /**
     * Get concatenated name
     *
     * @return string
     */
    public function getGiftCardBalanceAttribute()
    {
        /** @var GiftCard[]|Builder $giftCards */
        $giftCards = $this->giftCardsClaimed();

        if (!$giftCards->count()) {
            return 0;
        }

        return array_reduce($giftCards->get()->toArray(), function ($total, $giftCard) {
            return $total + $giftCard['remainingBalanceIfNotExpired'];
        });
    }

    /**
     * Get concatenated name
     *
     * @return Collection
     */
    public function getGiftCardsAttribute()
    {
        if (auth()->user() && auth()->user()->id === $this->id) {
            return $this->giftCardsClaimed()->get()->map(function ($giftcard) {
                return array_merge($giftcard->toArray(), ['token' => $giftcard->token]);
            });
        }
        return $this->giftCardsClaimed()->get();
    }

    public function getTheoryOnlineDiscountAttribute()
    {
        return $this->discountBenefits()->where('applied', true)
            ->where('beneficiary_segment_id', 32)->first();
    }


    /**
     * Send the password reset notification.
     *
     * @param  string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        Log::info("(event) Send password reset notification", [
            "class" => __CLASS__,
            "event" => "ResetPassword",
            "user" => ["id" => $this->id, "email" => $this->email],
        ]);

        Mail::send(new ResetPassword($this, $token));
    }
}
