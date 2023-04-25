<?php namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\Order;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class OrderCancelled
 * @property KKJTelegramBotService telegramBotService
 * @package Jakten\Mail
 */
class OrderCancelled extends AbstractMail
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
        return $this->markdown('email::order.cancelled')
            ->to($this->order->user->email)->subject('Avbokningsbekräftelse Körkortsjakten');
    }
}
