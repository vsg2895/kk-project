<?php

namespace Jakten\Modules\TelegramBot\Commands;

use Illuminate\Support\Facades\Log;
use Jakten\Helpers\TelegramBotHelper;
use Jakten\Models\Order;
use Jakten\Models\OrderItem;
use Telegram\Bot\Commands\Command;

class OrderManageCommand extends Command
{

    use TelegramBotHelper;

    /**
     * @var string
     */
    protected $name = 'order_manage';

    /**
     * @var string
     */
    protected $description = 'Manage the order';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        if (!$this->can($this->getUser())) {
            $this->replyWithChatAction(['action' => 'typing']);
            return $this->replyWithMessage(['text' => 'Sorry, but You are not allowed to perform this operation']);
        }

        list($env, $action, $ordersList) = explode(' ', $arguments);
        $ordersList = explode(',', $ordersList);

        if ($env !== env('APP_ENV')) {
            Log::info("Wanted env : {$env}. Current env : " . env('APP_ENV'));
            return false;
        }

        Log::info("Started the run on {$env}");

        $orders = Order::query()
            ->whereIn('id', $ordersList);

        $ordersItems = OrderItem::query()
            ->where('order_id', $ordersList);

        switch ($action) {
            case 'cancel':
                $orders->update(['cancelled' => true]);
                $ordersItems->update(['cancelled' => true, 'credited' => true]);

                return $this->replyWithMessage(['text' => 'Orders has been updated with the `Cancel` status!', 'parse_mode' => 'markdown']);
                break;
            case 'activate':
                $orders->update(['handled' => true]);
                $ordersItems->update(['delivered' => true]);

                return $this->replyWithMessage(['text' => 'Orders has been updated with the `Activated` status!', 'parse_mode' => 'markdown']);
                break;
        }
    }
}
