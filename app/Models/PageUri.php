<?php namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PageUri
 * @package Jakten\Models
 */
class PageUri extends Model
{
    const ACTIVE = 1;
    const REDIRECT = 2;
    const REMOVED = 4;

    /**
     * @var string The database table used by the model.
     */
    protected $table = 'pages_uris';

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = ['id', 'page_id', 'uri', 'status'];

    /**
     * @var array $dates
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * @var array $hidden
     */
    protected $hidden = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    /**
     * @param $value
     */
    public function setUriAttribute($value)
    {
        $this->attributes['uri'] = $this->addLeadingSlash($value);
    }

    /**
     * @param $uri
     * @return string
     */
    protected function addLeadingSlash($uri)
    {
        $slash = mb_substr($uri, 0, 1, 'utf-8');
        if ($slash !== '/') {
            return '/' . $uri;
        }

        return $uri;
    }
}
