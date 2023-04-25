<?php namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class SchoolUsp
 *
 * @property int id
 * @property string text
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class SchoolUsps extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'school_usps';

    /**
     * @var array $fillable
     */
    protected $fillable = ['text', 'school_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
