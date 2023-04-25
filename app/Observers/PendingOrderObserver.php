<?php namespace Jakten\Observers;

use Jakten\Models\PendingExternalOrder;
use Jakten\Services\Log\Contracts\ActionLogInterface;

/**
 * Class PendingOrderObserver
 * @package Jakten\Observers
 */
class PendingOrderObserver
{
    /**
     * @property ActionLogInterface $logService
     */
    protected $actionLogService;
    
    /**
     * PendingOrderObserver constructor.
     * @param ActionLogInterface $actionLogService
     */
    public function __construct(ActionLogInterface $actionLogService)
    {
        $this->actionLogService = $actionLogService;
    }

    /**
     * Listen to the User created event.
     *
     * @param PendingExternalOrder $order
     */
    public function created(PendingExternalOrder $order)
    {
        $this->actionLogService->logCreate($order);
    }
    
    /**
     * @param PendingExternalOrder $order
     */
    public function deleted(PendingExternalOrder $order)
    {
        $this->actionLogService->logDelete($order);
    }
}
