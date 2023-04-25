<?php

namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Jakten\Helpers\BladeSvgIcon\Icon;
use Jakten\Helpers\BladeSvgIcon\IconFactory;

/**
 * Class Partner
 * @package Jakten\Models
 * @property integer id
 * @property integer asset_id
 * @property string partner
 * @property string short_description
 * @property string image_type
 * @property string image
 * @property string url
 * @property bool active
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Asset asset
 *
 */
class Partner extends Model
{
    protected $fillable = [
        'partner',
        'short_description',
        'image_type',
        'image',
        'asset_id',
        'url',
        'active'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'active' => 'bool'
    ];

    /**
     * @return string
     */
    public function getActivityIconAttribute()
    {
        return new Icon($this->active ? 'check' : 'cross', 'inline', IconFactory::class);
    }

    /**
     * @param $active
     */
    public function setActiveAttribute($active)
    {
        $this->attributes['active'] = $active == 'on' ? true : false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Asset
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * @return string
     */
    public function getImageAttribute()
    {
        return $this->image_type == 'file' ?
            $this->asset->path : $this->attributes['image'];
    }
}
