<?php

namespace Jakten\Providers;

use Illuminate\Support\ServiceProvider;
use Jakten\Services\Log\ActionLogService;
use Jakten\Services\Log\Contracts\ActionLogInterface;

class ActionLogServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ActionLogInterface::class, ActionLogService::class);
    }
    
    /**
     * @return array
     */
    public function provides()
    {
        return [ActionLogInterface::class];
    }
}