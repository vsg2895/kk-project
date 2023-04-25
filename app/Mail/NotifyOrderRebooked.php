<?php

namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\Order;
use Jakten\Models\User;

class NotifyOrderRebooked extends AbstractMail
{
    use ClassResolver;

    public $delay = 2;

    /**
     * @var Order
     */
    public $order;

    public $admin;

    /**
     * Create a new message instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order, User $admin)
    {
        $this->setQueue();
        $this->order = $order;
        $this->admin = $admin;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email::notify.rebooked')
            ->to($this->admin->email)->subject('Beställning Ombokad');
    }
}
