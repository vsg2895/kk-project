<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ContactRequest
 *
 * @property int id
 * @property School school
 * @property int school_id
 * @property string message
 * @property string name
 * @property string title
 * @property string email
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ContactRequest extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'contact_requests';

    /**
     * @var array $fillable
     */
    protected $fillable = ['school_id', 'message', 'name', 'title', 'email'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
