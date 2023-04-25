<?php namespace Jakten\Models;

use Carbon\Carbon;
use Jakten\Facades\Auth;
use Jakten\Helpers\KlarnaSignup;
//use Jakten\Services\Asset\AnnotationType;
use Jakten\Services\Annotation\AnnotationType;
use Illuminate\Database\Eloquent\{Collection, Model, SoftDeletes};

/**
 * Class Organization
 *
 * @property int id
 * @property string name
 * @property string org_number
 * @property string payment_id
 * @property string payment_secret
 * @property string sign_up_status
 * @property string external_sign_up_id
 * @property string sign_up_rejected_reason
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property User[]|Collection users
 * @property School[]|Collection schools
 * @property SchoolClaim[]|Collection claims
 * @property Annotation[]|Collection comments
 */
class Organization extends Model
{
    use SoftDeletes;

    /**
     * @var string $table
     */
    protected $table = 'organizations';

    /**
     * @var array $fillable
     */
    protected $fillable = ['org_number', 'name', 'external_sign_up_id', 'sign_up_status', 'payment_secret', 'payment_id', 'sign_up_rejected_reason', 'sign_up_status_text', 'address'];

    /**
     * @var array $hidden
     */
    protected $hidden = ['external_sign_up_id', 'sign_up_status', 'payment_secret', 'payment_id', 'sign_up_rejected_reason', 'sign_up_status_text', 'comments'];

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting((function (Organization $organization) {
            $organization->users()->delete();
            $organization->schools()->delete();
            $organization->claims()->delete();
        }));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schools()
    {
        return $this->hasMany(School::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function claims()
    {
        return $this->hasMany(SchoolClaim::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function logo()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function comments()
    {
        return $this->belongsToMany(Annotation::class, 'organization_annotations')
            ->withTimestamps()
            ->orderBy('annotations.created_at', 'desc');
    }

    /**
     * @param $type
     * @return mixed
     */
    public function commentsByType($type)
    {
        return $this->comments()->where('type', $type);
    }

    /**
     * @return bool
     */
    public function hasKlarnaSignupError()
    {
        if ($this->sign_up_status != KlarnaSignup::STATUS_NOT_INITIATED) {
            return false;
        }

        $now = new Carbon();
        $comments = $this
            ->commentsByType(AnnotationType::KLARNA_ONBOARDING_ERROR)
            ->where('annotations.created_at', '>', $now->subWeek());

        if ($comments->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get an attribute array of all arrayable values.
     *
     * @param  array $values
     * @return array
     */
    protected function getArrayableItems(array $values)
    {
        if (count($this->getVisible()) > 0) {
            $values = array_intersect_key($values, array_flip($this->getVisible()));
        }

        if (count($this->getHidden()) > 0 && !$this->showAllData()) {
            $values = array_diff_key($values, array_flip($this->getHidden()));
        }

        return $values;
    }

    /**
     * @return bool
     */
    protected function showAllData()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return true;
            } elseif (Auth::user()->isOrganizationUser() && Auth::user()->organization_id == $this->attributes['id']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getPaymentIdAttribute()
    {
        return config('klarna.kkj_payment_id');
    }

    /**
     * @return string
     */
    public function getPaymentSecretAttribute()
    {
        return config('klarna.kkj_payment_secret');
    }
}
