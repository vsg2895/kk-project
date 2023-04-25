<?php namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Jakten\Services\Asset\AssetType;

/**
 * Class PostMultimedia
 *
 * @package Jakten\Models
 */
class PostMultimedia extends Model
{
    /**
     * @var array $fillable
     */
    protected $fillable = [
        'post_id',
        'path',
        'alt_text',
        'type',
    ];

    protected $casts = [
        'type' => 'int',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        return $this->type === AssetType::IMAGE_POST;
    }

    /**
     * @return bool
     */
    public function isVideo()
    {
        return $this->type === 7 || $this->isYouTube();
    }

    public function isYouTube()
    {
        return $this->type === 8;
    }

    /**
     * @return string|null
     */
    public function getMultimediaUrlAttribute()
    {
        $url = null;
        if ($this->isImage()) {
            $path = Storage::disk('upload')->url($this->path);
            $url = asset($path);
        } elseif ($this->isVideo()) {
            $url = $this->path;
        }

        return $url;
    }
}
