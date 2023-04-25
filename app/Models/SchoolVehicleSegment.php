<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SchoolVehicleSegment
 *
 * @property int id
 * @property int vehicle_segment_id
 * @property int school_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class SchoolVehicleSegment extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'schools_vehicle_segments';

    /**
     * @var array $fillable
     */
    protected $fillable = ['school_id', 'vehicle_segment_id'];
}
