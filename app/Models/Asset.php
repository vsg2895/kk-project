<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Jakten\Services\Asset\AssetType;

/**
 * Page entity.
 *
 *
 * @property int id
 * @property string path
 * @property string mime
 * @property int type
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property User author
 */
class Asset extends Model
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'assets';

    /**
     * @var array $fillable
     */
    protected $fillable = ['id', 'parent_id', 'path', 'mime', 'type', 'author_id'];

    /**
     * @var array $dates
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function versions()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @param $type
     * @return mixed
     */
    public function version($type)
    {
        return $this->versions->first(function ($asset) use ($type) {
            return $asset->type == AssetType::labelToTypeId($type, $asset);
        });
    }

    /**
     * @return string
     */
    public function getPathAttribute()
    {
        switch (env('APP_ENV')) {
            case 'local':
            case 'staging':
                switch ($this->type) {
                    default:
                        return "{$this->attributes['path']}";
                        break;
                    case 7:
                        return "/storage/upload/{$this->attributes['path']}";
                        break;
                }
                break;
            default:
                return "/storage/upload/{$this->attributes['path']}";
                break;
        }
    }
}
