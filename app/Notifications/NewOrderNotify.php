<?php namespace Jakten\Notifications;

use Jakten\Events\NewOrder;
use Jakten\Mail\NotifyNewOrder;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class NewOrderNotify
 * @package Jakten\Notifications
 */
class NewOrderNotify extends Notification
{
    use NotifyTrait;

    /**
     * @var NewOrder
     */
    protected $event;

    /**
     * NewOrderNotify constructor.
     *
     * @param NewOrder $event
     */
    public function __construct(NewOrder $event)
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
            'message' => __('notify.dash_new_order', [
                    'date' => $this->event->order->created_at,
                    'id' => $this->event->order->id
                ])
        ]);
    }

    /**
     * Create message class for ['mail'] channel.
     *
     * @param $user
     * @return NotifyNewOrder
     */
    public function toCustomMail($user)
    {
        return new NotifyNewOrder($user, $this->event->order);
    }

    /**
     * Create message class for [CustomSMSChannel::class] channel.
     *
     * @return string
     */
    public function toCustomSMS()
    {
        return __('notify.notification') . __('notify.new_order');
    }
}
