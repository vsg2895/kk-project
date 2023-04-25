<?php namespace Jakten\Notifications;

use Jakten\Events\OrderFailed;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Jakten\Mail\NotifyFailed;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class OrderFailedNotify
 * @package Jakten\Notifications
 */
class OrderFailedNotify extends Notification
{
    use NotifyTrait;

    /**
     * @var OrderFailed
     */
    protected $event;

    /**
     * OrderFailedNotify constructor.
     *
     * @param OrderFailed $event
     */
    public function __construct(OrderFailed $event)
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
            'message' => sprintf('%s for user id &lsaquo;%s&rsaquo;. PaymentId: %s',
                $this->event->label,
                $this->event->user->id,
                $this->event->klarnaPaymentId
            )
        ]);
    }

    /**
     * Create message class for ['mail'] channel.
     *
     * @param $user
     * @return NotifyFailed
     */
    public function toCustomMail($user)
    {
        return new NotifyFailed($user, $this->event);
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
