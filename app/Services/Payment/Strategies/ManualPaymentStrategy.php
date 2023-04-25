<?php
namespace Jakten\Services\Payment\Strategies;

use Illuminate\Support\Facades\Log;
use Jakten\Models\Order;

class ManualPaymentStrategy implements PaymentStrategyContract
{
    public function pay(Order $order)
    {
        Log::info('PAID WITH MANUAL PAYMENT');
    }
}
