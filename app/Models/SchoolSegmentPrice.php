<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SchoolSegmentPrice
 *
 * @property int id
 * @property int vehicle_segment_id
 * @property int school_id
 * @property int|null quantity
 * @property string|null comment
 * @property VehicleSegment segment
 * @property int amount
 * @property bool subject_to_change
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class SchoolSegmentPrice extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'school_segment_prices';

    /**
     * @var array $fillable
     */
    protected $fillable = ['school_id', 'vehicle_segment_id', 'quantity', 'comment', 'amount', 'subject_to_change', 'sort_order', 'fee'];

    /**
     * @var array $with
     */
    protected $with = ['segment'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function segment()
    {
        return $this->belongsTo(VehicleSegment::class, 'vehicle_segment_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get default price if none is set
     *
     * @param $value
     *
     * @return int
     */
    public function getAmountAttribute($value)
    {
        //if (is_null($value)) {
        //    return $this->segment->default_price;
        //}

        return $value;
    }

    /**
     * Get default comment if none is set
     *
     * @param $value
     *
     * @return string
     */
    public function getCommentAttribute($value)
    {
        //if (is_null($value)) {
        //    return $this->segment->default_comment;
        //}

        return $value;
    }
}
