<?php namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\{Order, User};
use Jakten\Services\KKJTelegramBotService;

/**
 * Class NotifyNewOrder
 * @property KKJTelegramBotService telegramBotService
 * @package Jakten\Mail
 */
class NotifyNewOrder extends AbstractMail
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
     * @param Order $order
     */
    public function __construct(User $notifyUser, Order $order)
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
        return $this->markdown('email::notify.created', ['order' => $this->order])
            ->to($this->notifyUser->email)->subject(__('notify.notification') . __('notify.new_order'));
    }
}
