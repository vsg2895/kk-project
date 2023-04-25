<?php

namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Log
 * @package Jakten\Models
 * @property null|string $comment
 */
class Log extends Model
{
    /**
     * @var array
     */
    public $fillable = ['comment'];
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function loggable()
    {
        return $this->morphTo();
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
