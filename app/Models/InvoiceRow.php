<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Jakten\Helpers\Prices;

/**
 * Class InvoiceRow
 *
 * @property int id
 * @property int|null invoice_id
 * @property Invoice|null invoice
 * @property string name
 * @property int amount
 * @property int quantity
 * @property int price
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class InvoiceRow extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'invoice_rows';

    /**
     * @var array $fillable
     */
    protected $fillable = ['invoice_id', 'name', 'quantity', 'amount'];

    /**
     * @var array $appends
     */
    protected $appends = ['price', 'price_with_currency'];

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
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param $value
     * @return float|int
     */
    public function getPriceAttribute($value)
    {
        return $this->amount * $this->quantity;
    }

    /**
     * @param $value
     * @return string
     */
    public function getPriceWithCurrencyAttribute($value)
    {
        return $this->price . Prices::CURRENCY_SUFFIX;
    }
}
