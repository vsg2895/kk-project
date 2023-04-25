<?php namespace Jakten\Notifications;

use Jakten\Events\OrderCancelled;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Jakten\Mail\NotifyCancelled;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class OrderCancelledNotify
 * @package Jakten\Notifications
 */
class OrderCancelledNotify extends Notification
{
    use NotifyTrait;

    /**
     * @var OrderCancelled
     */
    protected $event;

    /**
     * OrderCancelledNotify constructor.
     *
     * @param OrderCancelled $event
     */
    public function __construct(OrderCancelled $event)
    {
        $this->event = $event;
    }

    /**
     * Create message class for ['database'] channel.
     *
     * @param  mixed  $user
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     */
    public function toDatabase($user)
    {
        return new DatabaseMessage([
            'message' => sprintf('%s. OrderId: %d',
                $this->event->label,
                $this->event->order->id
            )
        ]);
    }

    /**
     * Create message class for ['mail'] channel.
     *
     * @param $user
     * @return NotifyCancelled
     */
    public function toCustomMail($user)
    {
        return new NotifyCancelled($user, $this->event->order);
    }

    /**
     * Create message class for [CustomSMSChannel::class] channel.
     *
     * @return string
     */
    public function toCustomSMS()
    {
        return __('notify.notification') . __('notify.' . $this->event->label);
    }

}
