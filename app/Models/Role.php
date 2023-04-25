<?php namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * @package Jakten\Models
 */
class Role extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'roles';

    /**
     * @var array $fillable
     */
    protected $fillable = ['role_id', 'name'];

    /**
     * @var array $dates
     */
    protected $dates = ['created_at', 'updated_at'];
}