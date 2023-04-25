<?php namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class NotifySetting
 * @package Jakten\Models
 */
class NotifySetting extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'notify_settings';

    /**
     * @var array $fillable
     */
    protected $fillable = ['notify_id', 'user_id', 'channels'];

    /**
     * @var array $dates
     */
    protected $dates = ['created_at', 'updated_at'];

//    public function notify()
//    {
//        return $this->belongsTo('Jakten\Models\NotifyEvents', 'notify_id');
//    }
//
//    public function user()
//    {
//        return $this->belongsTo('Jakten\Models\User', 'user_id');
//    }

}