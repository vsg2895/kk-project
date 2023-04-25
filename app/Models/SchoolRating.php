<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SchoolRating
 *
 * @property int id
 * @property School school
 * @property User user
 * @property Course course
 * @property int course_id
 * @property int user_id
 * @property int rating
 * @property Carbon school_notified
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class SchoolRating extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'school_ratings';

    /**
     * @var array $fillable
     */
    protected $fillable = ['rating', 'title', 'content', 'verified', 'school_notified'];

    /**
     * @var array $with
     */
    protected $with = ['user'];
    /**
     * @var \Illuminate\Support\Carbon|mixed
     */
    private $school_notified;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
