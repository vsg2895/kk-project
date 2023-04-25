<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\{Collection, Model};

/**
 * Class Invoice
 *
 * @property int id
 * @property int school_id
 * @property School school
 * @property Order|null order
 * @property bool sent
 * @property bool paid
 * @property InvoiceRow[]|Collection rows
 * @property Carbon paid_at
 * @property Carbon sent_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Invoice extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'invoices';

    /**
     * @var array $fillable
     */
    protected $fillable = ['school_id', 'sent', 'sent_at', 'paid_at'];

    /**
     * @var array $dates
     */
    protected $dates = ['created_at', 'updated_at', 'sent_at', 'paid_at'];

    /**
     * @var array $with
     */
    protected $with = ['rows'];

    /**
     * @var array $appends
     */
    protected $appends = ['paid', 'sent'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rows()
    {
        return $this->hasMany(InvoiceRow::class);
    }

    /**
     * @param $value
     * @return bool
     */
    public function getPaidAttribute($value)
    {
        return !is_null($this->paid_at);
    }

    /**
     * @param $value
     * @return bool
     */
    public function getSentAttribute($value)
    {
        return !is_null($this->sent_at);
    }
}
