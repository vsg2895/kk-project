<?php namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class NotifyEvent
 * @package Jakten\Models
 */
class NotifyEvent extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'notify_events';

    /**
     * @var array $fillable
     */
    protected $fillable = ['role_id', 'label', 'available'];

    /**
     * @var array $dates
     */
    protected $dates = ['created_at', 'updated_at'];

//    public function role()
//    {
//        return $this->belongsTo('Jakten\Models\Roles', 'role_id', 'role_id');
//    }
//
//    public function notifications()
//    {
//        return $this->hasmany('Jakten\Models\NotifySettings', 'notify_id');
//    }
}
