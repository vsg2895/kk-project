<?php namespace Jakten\Models;

use Carbon\Carbon;
use Jakten\Helpers\PhoneNumber;
use Jakten\Formatters\SchoolFormatter;
use Illuminate\Database\Eloquent\{
    Collection, Model, SoftDeletes
};

/**
 * Class School
 *
 * @property int id
 * @property int city_id
 * @property City city
 * @property string name
 * @property string address
 * @property string coaddress
 * @property int zip
 * @property int|null organization_id
 * @property string postal_city
 * @property string phone_number
 * @property string contact_email
 * @property string booking_email
 * @property string website
 * @property string full_address
 * @property string description
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property string slug
 * @property float|null average_rating
 * @property int rating_count
 * @property float longitude
 * @property float latitude
 * @property bool has_klarna
 * @property bool accepts_gift_card
 * @property bool host_digital
 * @property SchoolUsps[]|Collection usps
 * @property SchoolRating[]|Collection ratings
 * @property SchoolSegmentPrice[]|Collection prices
 * @property Course[]|Collection courses
 * @property Organization|null organization
 * @property Vehicle[]|Collection availableVehicles
 * @property VehicleSegment[]|Collection segments
 * @property Addon[]|Collection addons
 * @property Annotation[]|Collection comments
 * @property Asset logo
 * @property int logo_id
 * @property int reco_id
 * @property bool reco_enabled
 * @property string reco_url
 * @property bool hasReco
 * @property string default_course_description
 * @property string default_course_confirmation_text
 * @property string bankgiro_nubmer
 * @property string organization_number
 * @property string moms_reg_nr
 * @property string loyalty_level
 */
class School extends Model
{
    use SoftDeletes;

    /**
     * @var string $table
     */
    protected $table = 'schools';

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'city_id', 'name', 'address', 'zip', 'postal_city', 'phone_number', 'contact_email',
        'booking_email', 'website', 'description', 'organization_id', 'latitude', 'longitude', 'average_rating',
        'rating_count', 'default_course_description', 'default_course_confirmation_text', 'coaddress', 'not_member', 'accepts_gift_card',
        'top_deal', 'show_left_seats', 'left_seats', 'reco_id', 'reco_url', 'reco_enabled', 'logo_id', 'host_digital',
        'bankgiro_number', 'organization_number', 'moms_reg_nr', 'loyalty_level', 'top_partner', 'loyalty_fixed_amount',
        'connected',
    ];

    /**
     * @var array $appends
     */
    protected $appends = ['formatted_prices', 'slug', 'has_klarna', 'full_address', 'has_reco'];

    /**
     * @var array $hidden
     */
    protected $hidden = ['comments', 'prices'];

    /**
     * @var array $with
     */
    protected $with = ['images', 'organization'];

    protected $casts = [
        'organization_id' => 'int',
        'reco_enabled' => 'bool',
        'has_reco' => 'bool'
    ];

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting((function (School $school) {
            $school->courses()->delete();
        }));
    }

    /**
     * @return mixed
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function segments()
    {
        return $this->belongsToMany(VehicleSegment::class, 'schools_vehicle_segments', 'school_id', 'vehicle_segment_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usps()
    {
        return $this->hasMany(SchoolUsps::class);
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
    public function allRatings()
    {
        return $this->hasMany(SchoolRating::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function availableCourses()
    {
        return $this->hasMany(Course::class)
            ->where('seats','>',0)
            ->where('start_time','>',Carbon::now()->format('Y-m-d H-i-s'));
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function upcomingCourses()
    {
        return $this->hasMany(Course::class)
            ->whereDate('start_time', '>=', Carbon::now('Europe/Stockholm')
                ->format("Y-m-d H:i:s")
            );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prices()
    {
        return $this->hasMany(SchoolSegmentPrice::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customAddons()
    {
        return $this->hasMany(CustomAddon::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function availableVehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'schools_vehicles');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function calculatedPrices()
    {
        return $this->hasMany(SchoolCalculatedPrice::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'schools_addons', 'school_id', 'addon_id')
            ->withPivot('price', 'description', 'top_deal', 'show_left_seats', 'left_seats', 'sort_order', 'fee');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function claims()
    {
        return $this->hasMany(SchoolClaim::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function comments()
    {
        return $this->belongsToMany(Annotation::class, 'school_annotations')
            ->withTimestamps()
            ->orderBy('school_annotations.created_at', 'desc');
    }

    /**
     * @return SchoolFormatter
     */
    public function formatter()
    {
        return new SchoolFormatter($this);
    }

    /**
     * @param $value
     * @return float
     */
    public function getAverageRatingAttribute($value)
    {
        return round($value, 1);
    }

    /**
     * @param $value
     * @return string
     */
    public function getBookingEmailAttribute($value)
    {
        return $value;
    }

    /**
     * @param null $vehicle
     * @return array
     */
    public function getFormattedPricesAttribute($vehicle = null)
    {
        return $this->formatter()->prices($vehicle);
    }

    /**
     * @return string
     */
    public function getSlugAttribute()
    {
        return $this->formatter()->slug();
    }

    /**
     * @return bool
     */
    public function getHasKlarnaAttribute()
    {
        return $this->organization && $this->organization->payment_id && $this->organization->payment_secret;
    }

    /**
     * @param $value
     */
    public function setZipAttribute($value)
    {
        $this->attributes['zip'] = preg_replace('/\D/', '', $value);
    }

    /**
     * @param $value
     */
    public function setWebsiteAttribute($value)
    {
        $url = '';
        if (!str_contains($value, 'http://') && !str_contains($value, 'https://')) {
            $url = 'http://';
        }

        $url .= $value;

        $this->attributes['website'] = $url;
    }

    /**
     * @param $value
     */
    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['phone_number'] = PhoneNumber::parsePhoneNumber($value);
    }

    /**
     * @return string
     */
    public function getFullAddressAttribute()
    {
        if (strlen($this->attributes['postal_city']) === 0 && strlen($this->attributes['address']) === 0) {
            return '-';
        }

        if (strlen($this->attributes['postal_city']) === 0) {
            return $this->attributes['address'];
        }

        if (strlen($this->attributes['address']) === 0) {
            return ucfirst(mb_strtolower($this->attributes['postal_city']));
        }

        return ucfirst(mb_strtolower($this->attributes['postal_city'])) . ", {$this->attributes['address']}";
    }

    /**
     * Checks, if the school has Reco reviews display ability
     *
     * @return bool
     */
    public function getHasRecoAttribute()
    {
        return $this->reco_id && $this->reco_url && $this->hasKlarna();
    }

    /**
     * @return bool
     */
    public function hasKlarna()
    {
        if ($this->organization) {
            return $this->organization && $this->organization->payment_id && $this->organization->payment_secret;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isMember()
    {
        return !is_null($this->organization_id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function logo()
    {
        return $this->belongsTo(Asset::class);
    }

    public function images()
    {
        return $this->hasMany(SchoolImage::class);
    }
}
