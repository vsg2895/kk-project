<?php

namespace Jakten\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Jakten\Helpers\ClassResolver;
use Jakten\Models\Order;

class OrderRebooked extends AbstractMail
{
    use ClassResolver;

    public $delay = 2;

    /**
     * @var Order
     */
    public $order;

    /**
     * Create a new message instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->setQueue();
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email::order.rebooked')
            ->to($this->order->user->email)->subject('Beställning Ombokad');
    }
}
