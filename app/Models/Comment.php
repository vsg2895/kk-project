<?php namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Comment
 * @package Jakten\Models
 */
class Comment extends Model
{
    /**
     * @var array $fillable
     */
    protected $fillable = [
        'post_id',
        'user_id',
        'text',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
