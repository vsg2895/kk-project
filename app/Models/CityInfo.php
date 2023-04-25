<?php namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CityInfo
 */
class CityInfo extends Model
{
    /**
     * @var array $fillable
     */
    protected $fillable = [
        'desc_trafikskolor',
        'desc_introduktionskurser',
        'desc_riskettan',
        'desc_teorilektion',
        'desc_risktvaan',
        'desc_riskettanmc',
        'city_id'
    ];

    /**
     * @return mixed
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
