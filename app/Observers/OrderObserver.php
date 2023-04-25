<?php namespace Jakten\Observers;

use Illuminate\Support\Facades\Log;
use Jakten\Jobs\ActivatePackageOrders;
use Jakten\Models\Order;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\Log\Contracts\ActionLogInterface;
use Jakten\Services\Payment\Klarna\KlarnaService;

/**
 * Class OrderObserver
 * @package Jakten\Observers
 */
class OrderObserver
{
    /**
     * @property ActionLogInterface $logService
     */
    protected $actionLogService;

    /** @var KKJTelegramBotService */
    private $botService;

    /**
     * @var KlarnaService
     */
    private $klarnaService;

    /**
     * OrderObserver constructor.
     * @param ActionLogInterface $actionLogService
     * @param KKJTelegramBotService $botService
     * @param KlarnaService $klarnaService
     */
    public function __construct(ActionLogInterface $actionLogService, KKJTelegramBotService $botService, KlarnaService $klarnaService)
    {
        $this->actionLogService = $actionLogService;
        $this->botService = $botService;
        $this->klarnaService = $klarnaService;
    }

    /**
     * Listen to the User created event.
     *
     * @param Order $order
     */
    public function created(Order $order)
    {
        $this->actionLogService->logCreate($order);
        $this->botService->log('order_created', compact('order'));
        // Mail::send(new OrderCreated($order));
    }

    /**
     * @param Order $order
     */
    public function deleted(Order $order)
    {
        $this->actionLogService->logDelete($order);
    }
}
