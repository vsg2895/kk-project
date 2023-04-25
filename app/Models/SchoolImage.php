<?php

namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Class SchoolImage
 * @package Jakten\Models
 * @property $url
 * @property $thumbnailUrl
 */
class SchoolImage extends Model
{
    /**
     * @var array $fillable
     */
    protected $fillable = ['file_name', 'alt_text'];

    protected $appends = ['url'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
    
    /**
     * @return string
     */
    public function getUrlAttribute()
    {
        return asset(Storage::url($this->file_name));
    }
    
    /**
     * @return string
     */
    public function getThumbnailUrlAttribute()
    {
        $fileExtension = '.' . pathinfo($this->file_name)['extension'];
        $fileName = str_replace_last($fileExtension, '_thumbnail' . $fileExtension, $this->file_name);
        if (!Storage::exists($fileName)) {
            return asset(Storage::url($this->file_name));
        }
        
        return asset(Storage::url($fileName));
    }
}
