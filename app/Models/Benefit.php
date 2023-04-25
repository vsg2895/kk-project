<?php

namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Benefit extends Model
{
    use SoftDeletes;

    protected $fillable = ['benefit_type', 'amount', 'claimed', 'applied', 'user_id', 'order_id', 'vehicle_segment_id', 'beneficiary_segment_id', 'addon_id'];
}
