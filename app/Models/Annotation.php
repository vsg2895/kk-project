<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Annotation
 *
 * @property int id
 * @property string message
 * @property int user_id
 * @property User user
 * @property string type
 * @property array data
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Annotation extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'annotations';

    /**
     * @var array $fillable
     */
    protected $fillable = ['user_id', 'type', 'message', 'data'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * @param $value
     */
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = json_encode($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @return mixed
     */
    public function getData() {
        return json_decode($this->data);
    }
}
