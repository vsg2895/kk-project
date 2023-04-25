<?php

namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TelegramWebHook
 * @package Jakten\Models
 * @property string url
 */
class TelegramWebHook extends Model
{
    protected $fillable = [
        'url'
    ];
}
