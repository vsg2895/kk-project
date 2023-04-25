<?php namespace Jakten\Models;

use Carbon\Carbon;
use Jakten\Helpers\Prices;
use Illuminate\Database\Eloquent\{Collection, Model, SoftDeletes};

/**
 * Class Course
 *
 * @property int id
 * @property int vehicle_segment_id
 * @property VehicleSegment segment
 * @property School school
 * @property City city
 * @property int city_id
 * @property Carbon start_time
 * @property int school_id
 * @property int length_minutes
 * @property string address
 * @property string address_description
 * @property string description
 * @property string confirmation_text
 * @property int seats
 * @property int price
 * @property string name
 * @property string course_address
 * @property double latitude
 * @property double longitude
 * @property int zip
 * @property string part
 * @property string postal_city
 * @property string transmission
 * @property OrderItem[]|Collection bookings
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Course extends Model
{
    use SoftDeletes;

    /**
     * @var string $table
     */
    protected $table = 'courses';

    /**
     * @var array $fillable
     */
    protected $fillable = ['school_id', 'vehicle_segment_id', 'city_id', 'start_time', 'length_minutes', 'price', 'address', 'transmission',
        'address_description', 'description', 'confirmation_text', 'seats', 'created_at', 'latitude', 'longitude', 'zip',
        'postal_city', 'digital_shared', 'part', 'old_price'];

    /**
     * @var array $dates
     */
    protected $dates = ['created_at', 'updated_at', 'start_time'];

    /**
     * @var array $with
     */
    protected $with = ['segment'];

    /**
     * @var array $appends
     */
    protected $appends = ['start_hour', 'end_hour', 'price_with_currency', 'name', 'available_seats', 'course_address'];

    /**
     * @param $date
     */
    public function setStartTimeAttribute($date)
    {
        $this->attributes['start_time'] = Carbon::createFromFormat('Y-m-d H:i', $date);
    }

    /**
     * @return mixed
     */
    public function school()
    {
        return $this->belongsTo(School::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bookings()
    {
        return $this->belongsToMany(OrderItem::class, 'course_participants', 'course_id', 'order_item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function segment()
    {
        return $this->belongsTo(VehicleSegment::class, 'vehicle_segment_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|OrderItem[]
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @param $value
     * @return string
     */
    public function getStartHourAttribute($value)
    {
        $startTime = $this->start_time;

        return $startTime->format('H:i');
    }

    /**
     * @param $value
     * @return string
     */
    public function getEndHourAttribute($value)
    {
        $endTime = $this->start_time->copy()->addMinutes($this->length_minutes);

        return $endTime->format('H:i');
    }

    /**
     * @param $value
     * @return string
     */
    public function getPriceWithCurrencyAttribute($value)
    {
        return round($this->price) . Prices::CURRENCY_SUFFIX;
    }

    /**
     * @param $value
     * @return string
     */
    public function getNameAttribute($value)
    {
        return $this->segment->label;
    }

    /**
     * @param $value
     * @return int
     */
    public function getAvailableSeatsAttribute($value)
    {
        return $this->seats;
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
    public function setConfirmationTextAttribute($value)
    {
        $this->attributes['confirmation_text'] = $value ?: '';
    }

    /**
     * @return string
     */
    public function getCourseAddressAttribute()
    {
        return "{$this->address}, {$this->zip} {$this->postal_city}";
    }

}
