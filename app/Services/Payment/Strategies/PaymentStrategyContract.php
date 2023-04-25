<?php
namespace Jakten\Services\Payment\Strategies;

use Jakten\Models\Order;

interface PaymentStrategyContract
{
    public function pay(Order $order);
}
