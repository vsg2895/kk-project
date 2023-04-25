<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SchoolAddons
 *
 */
class SchoolsAddons extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'schools_addons';

    /**
     * @var array $fillable
     */
    protected $fillable = ['fee'];

}
