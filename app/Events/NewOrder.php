<?php namespace Jakten\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Jakten\Models\Order;

/**
 * Class NewOrder
 * @package Jakten\Events
 */
class NewOrder
{
    use Dispatchable, SerializesModels;

    /**
     * @var Order
     */
    public $order;

    /**
     * @var string
     */
    public $label = 'new_order';

    /**
     * NewOrder constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
