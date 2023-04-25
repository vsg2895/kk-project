<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class County
 *
 * @property int id
 * @property string name
 * @property string slug
 * @property float lat
 * @property float lng
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class County extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'counties';

    /**
     * @var array $fillable
     */
    protected $fillable = ['name', 'slug', 'lat', 'lng'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
