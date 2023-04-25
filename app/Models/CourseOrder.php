<?php namespace Jakten\Models;

use Carbon\Carbon;
use Jakten\Helpers\Prices;
use Illuminate\Database\Eloquent\{Collection, Model, SoftDeletes};

/**
 * Class Course
 *
 * @property Carbon date
 * @property string school_ids
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class CourseOrder extends Model
{
    use SoftDeletes;

    /**
     * @var string $table
     */
    protected $table = 'courses_order';

    /**
     * @var array $fillable
     */
    protected $fillable = ['school_id', 'order', 'vehicle_segment', 'city_id', 'user_id', 'date', 'created_at'];

    /**
     * @var array $dates
     */
    protected $dates = ['created_at', 'updated_at', 'date'];

}
