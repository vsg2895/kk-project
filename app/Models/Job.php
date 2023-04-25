<?php

namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Job
 * @package Jakten\Models
 * @property integer id
 * @property string queue
 * @property array payload
 * @property integer attempts
 * @property Carbon reserved_at
 * @property Carbon available_at
 * @property Carbon created_at
 * @property string|int
 */
class Job extends Model
{

    protected $casts = [
        'payload' => 'json'
    ];

    protected $dates = [
        'created_at',
        'reserved_at',
        'available_at'
    ];

    protected $appends = [
        'starts_at'
    ];

    /**
     * @return int
     */
    public function getStartsAtAttribute()
    {
        if ($this->available_at->isFuture()) {
            return Carbon::now()->diffInSeconds($this->available_at);
        }

        return 0;
    }

}
