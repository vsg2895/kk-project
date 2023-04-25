<?php namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\User;
use Jakten\Events\OrderFailed;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class NotifyFailed
 * @property KKJTelegramBotService telegramBotService
 * @package Jakten\Mail
 */
class NotifyFailed extends AbstractMail
{

    use ClassResolver;

    /**
     * @var $notifyUser
     */
    protected $notifyUser;

    /**
     * @var $order
     */
    protected $order;

    /**
     * NotifyRegister constructor.
     *
     * @param User $notifyUser
     * @param OrderFailed $order
     */
    public function __construct(User $notifyUser, OrderFailed $order)
    {
        $this->setQueue();
        $this->notifyUser = $notifyUser;
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email::notify.failed', ['order' => $this->order])
            ->to($this->notifyUser->email)->subject(__('notify.notification') . __('notify.order_failed'));
    }
}
