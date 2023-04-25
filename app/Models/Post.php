<?php namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kyslik\ColumnSortable\Sortable;

/**
 * Class Post
 *
 * @package Jakten\Models
 */
class Post extends Model
{
    use Sortable;

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'user_id',
        'post_type',
        'title',
        'slug',
        'content',
        'footer_content',
        'button_text',
        'link',
        'status',
        'hidden',
        'preview_img_filename',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => 'int',
        'hidden' => 'int',
    ];

    /**
     * @var array
     */
    public $sortable = [
        'user_id',
        'title',
        'status',
        'created_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function multimedia()
    {
        return $this->hasMany(PostMultimedia::class);
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->status === 1;
    }

    /**
     * @return string
     */
    public function getPreviewImgFilenameUrlAttribute()
    {
        $url = null;
        if (!is_null($this->preview_img_filename)) {
            $path = Storage::disk('upload')->url($this->preview_img_filename);
            $url = asset($path);
        }

        return $url;
    }

    public function setSlugAttribute($slug)
    {
        $this->attributes['slug'] = str_replace([' ', '/'], '-', $slug);
    }
}
