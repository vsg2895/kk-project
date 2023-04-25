<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\{Collection, Model};

/**
 * Page entity.
 *
 *
 * @property int id
 * @property string title
 * @property string content
 * @property string meta_description
 * @property Carbon published_at
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property PageUri[]|Collection uris
 */
class Page extends Model
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'pages';

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = ['id', 'title', 'content', 'meta_description', 'published_at'];

    /**
     * @var array $dates
     */
    protected $dates = ['created_at', 'updated_at', 'published_at'];

    /**
     * @var array $hidden
     */
    protected $hidden = ['id'];

    /**
     * @var array
     */
    protected $with = ['uri'];

    /**
     * @return $this
     */
    public function uri()
    {
        return $this->hasOne(PageUri::class, 'page_id')->where('status', PageUri::ACTIVE);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uris()
    {
        return $this->hasMany(PageUri::class, 'page_id');
    }

    /**
     * @return null
     */
    public function getUri()
    {
        return $this->uri ? $this->uri->uri : null;
    }

    /**
     * @param $value
     */
    public function setMetaDescriptionAttribute($value)
    {
        $this->attributes['meta_description'] = $value ?: '';
    }

    // public function formatter()
    // {
    //     return new PageFormatter($this);
    // }

    /**
     * @return bool
     */
    public function isPublished()
    {
        if ($this->published_at) {
            return !$this->published_at->isFuture();
        }

        return false;
    }
}
