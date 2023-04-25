<?php namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SchoolCalculatedPrice
 *
 * @property int school_id
 * @property int comparison_price
 * @property int vehicle_id
 * @property int DRIVING_LESSON
 * @property int RISK_ONE
 * @property int RISK_TWO
 */
class SchoolCalculatedPrice extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'school_calculated_prices';
}
