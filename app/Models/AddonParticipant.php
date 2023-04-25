<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CourseParticipant
 *
 * @property int id
 * @property int order_item_id
 * @property int course_id
 * @property Course course
 * @property OrderItem booking
 * @property string given_name
 * @property string family_name
 * @property string social_security_number
 * @property string transmission
 * @property string type
 * @property Carbon created_at
 * @property Carbon updated_at
 */

class AddonParticipant extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'addon_participants';

    /**
     * @var array $fillable
     */
    protected $fillable = ['addon_id', 'order_item_id', 'given_name', 'family_name', 'social_security_number', 'email',
        'type', 'transmission', 'phone_number'];

    /**
     * @var array $appends
     */
    protected $appends = ['name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function setPhoneNumberAttribute($phoneNumber)
    {
        $this->attributes['phone_number'] = $phoneNumber ?: 0;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->given_name . ' ' . $this->family_name;
    }
}
