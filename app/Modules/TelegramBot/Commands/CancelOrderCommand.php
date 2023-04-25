<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 2019-08-22
 * Time: 00:05
 */

namespace Jakten\Modules\TelegramBot\Commands;


use Illuminate\Support\Facades\Log;
use Jakten\Helpers\TelegramBotHelper;
use Jakten\Models\Order;
use Jakten\Models\TelegramChat;
use Jakten\Services\OrderService;
use Jakten\Services\Payment\Klarna\KlarnaService;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Objects\Chat;
use Telegram\Bot\Objects\User;

class CancelOrderCommand extends Command
{

    use TelegramBotHelper;

    /**
     * @var string
     */
    protected $name = 'order_cancel';

    /**
     * @var string
     */
    protected $description = 'Order cancellation';

    /** @var OrderService $orderService */
    private $orderService;

    /** @var KlarnaService mixed */
    private $klarnaService;

    public function __construct()
    {
        $this->orderService = resolve(OrderService::class);
        $this->klarnaService = resolve(KlarnaService::class);
    }

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {

        $arguments = explode(' ', $arguments);

        /** @var TelegramChat $user */
        $user = $this->getUser();

        if (!$this->can($user)) {
            $this->replyWithChatAction(['action' => 'typing']);
            return $this->replyWithMessage(['parse_mode' => 'markdown', 'text' => "I'm sorry, but You're not allowed to perform this operation"]);
        }

        Log::info($arguments);

        if (is_array($arguments) && count($arguments) !== 2) {
            return $this->replyWithMessage([
                'text' => "Env and OrderID should be present as an arguments. Example:\r\n" .
                    "`order_cancel staging 290`",
                'parse_mode' => 'markdown'
            ]);
        }

        list($env, $orderId) = array_filter($arguments);

        if ($env !== env('APP_ENV')) {
            Log::info('Cancel Order Command : Can\'t be executed on this env');
            return false;
        }

        if ($user instanceof TelegramChat) {
            $this->replyWithChatAction(['action' => 'typing']);

            try {
                /** @var Order|null $order */
                $order = Order::query()
                    ->where('id', $orderId)->first();

                if (!$order || !$order instanceof Order) {
                    return $this->replyWithMessage(['parse_mode' => 'markdown', 'text' => "Sorry, but we can't find out the order `#{$orderId}` on the `{$env}` Environment"]);
                }

                $this->replyWithMessage(['parse_mode' => 'markdown', 'text' => 'Let me try to cancel it for You :)']);
                $this->replyWithChatAction(['action' => 'typing']);

                $this->klarnaService->refund($order, $order->items()->get());
                $this->orderService->cancelOrder($order);

                if ($order->cancelled) {
                    return $this->replyWithMessage(['parse_mode' => 'markdown', 'text' => 'Order has been successfully cancelled!']);
                }

                return $this->replyWithMessage(['parse_mode' => 'markdown', 'text' => "Something has went wrong while cancelling this order.\r\n" .
                    "I think, that we should get some information from Klarna about it."]);
            } catch (\Exception $exception) {
                return $this->replyWithMessage(['parse_mode' => 'markdown', 'text' => $exception->getMessage()]);
            }
        }
    }

}
