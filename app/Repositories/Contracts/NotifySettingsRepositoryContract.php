<?php namespace Jakten\Repositories\Contracts;

/**
 * Interface NotifySettingsRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface NotifySettingsRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param $formEvents
     * @param $notifyEvents
     * @return void
     */
    public function updateSettings($formEvents, $notifyEvents);
}
