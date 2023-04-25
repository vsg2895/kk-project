<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Addon
 *
 * @property int id
 * @property string name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Addon extends Model
{
    /**
     * @var string
     */
    protected $table = 'addons';

    /**
     * @var array
     */
    protected $fillable = ['name'];
}
