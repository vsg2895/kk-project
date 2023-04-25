<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\{Collection, Model};

/**
 * Class City
 *
 * @property int id
 * @property County county
 * @property string name
 * @property string slug
 * @property School[]|Collection schools
 * @property CityInfo[]|Collection info
 * @property float latitude
 * @property float longitude
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class City extends Model
{
    /**
     * @constant VALUES
     */
    const CITIES_EXCLUDE_FROM_ALGORITHM = [143];


    /**
     * @var string $table
     */
    protected $table = 'cities';

    /**
     * @var array $fillable
     */
    protected $fillable = ['county_id', 'name', 'slug', 'latitude', 'longitude', 'school_id', 'school_description', 'search_radius'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function county()
    {
        return $this->belongsTo(County::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schools()
    {
        return $this->hasMany(School::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bestDeals()
    {
        return $this->belongsToMany(School::class, 'city_best_deal_schools', 'city_id', 'school_id');
//        return $this->hasOne(School::class, 'id', 'school_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function info()
    {
        return $this->hasOne(CityInfo::class, 'city_id', 'id');
    }
}
