<?php
namespace Jakten\Services\Payment;

use Jakten\Helpers\Payment;
use Jakten\Services\Payment\Exceptions\StrategyNotFoundException;
use Jakten\Services\Payment\Strategies\ManualPaymentStrategy;
use Jakten\Services\Payment\Strategies\PaymentStrategyContract;

class PaymentService
{
    /**
     * @param $type
     *
     * @return PaymentStrategyContract
     *
     * @throws StrategyNotFoundException
     */
    public function getPaymentStrategy($type)
    {
        if ($type === Payment::MANUAL_PAYMENT) {
            return new ManualPaymentStrategy();
        }

        throw new StrategyNotFoundException();
    }
}
