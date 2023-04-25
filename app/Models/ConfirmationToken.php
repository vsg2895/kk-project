<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ConfirmationToken
 *
 * @property int id
 * @property string email
 * @property string token
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ConfirmationToken extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'confirmation_tokens';

    /**
     * @var array $fillable
     */
    protected $fillable = ['email', 'token'];
}
