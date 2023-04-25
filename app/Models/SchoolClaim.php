<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SchoolClaim
 *
 * @property int id
 * @property int school_id
 * @property School school
 * @property int organization_id
 * @property int user_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class SchoolClaim extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'school_claims';

    /**
     * @var array $fillable
     */
    protected $fillable = ['school_id', 'organization_id', 'user_id'];

    /**
     * @return mixed
     */
    public function school()
    {
        return $this->belongsTo(School::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class)->withTrashed();
    }
}
