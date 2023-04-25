<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};

/**
 * Class Addon
 *
 * @property int id
 * @property School school
 * @property string name
 * @property int price
 * @property string description
 * @property boolean active
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class CustomAddon extends Model
{
    use SoftDeletes;

    /**
     * @var string $table
     */
    protected $table = 'custom_addons';

    /**
     * @var array $fillable
     */
    protected $fillable = ['name','price','description', 'active', 'top_deal', 'show_left_seats', 'left_seats', 'fee'];

    /**
     * @var array $dates
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * @return mixed
     */
    public function school()
    {
        return $this->belongsTo(School::class)->withTrashed();
    }
}
