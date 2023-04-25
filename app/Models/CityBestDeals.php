<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Addon
 *
 * @property int id
 * @property string name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class CityBestDeals extends Model
{
    /**
     * @var string
     */
    protected $table = 'city_best_deal_schools';

}
