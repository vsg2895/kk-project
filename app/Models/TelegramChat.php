<?php

namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TelegramChat
 * @package Jakten\Models
 * @property string username
 * @property string full_name
 * @property integer chat_id
 * @property boolean enabled
 *
 */
class TelegramChat extends Model
{
    protected $fillable = [
        'username',
        'full_name',
        'chat_id',
        'enabled'
    ];

    protected $casts = [
        'chat_id' => 'integer',
        'enabled' => 'boolean'
    ];
}
