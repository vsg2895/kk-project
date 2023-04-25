<?php namespace Jakten\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Jakten\Models\Order;

/**
 * Class OrderCancelled
 * @package Jakten\Events
 */
class OrderCancelled
{
    use Dispatchable, SerializesModels;

    /**
     * @var Order
     */
    public $order;

    /**
     * @var string
     */
    public $label = 'order_cancelled';

    /**
     * NewOrder constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
