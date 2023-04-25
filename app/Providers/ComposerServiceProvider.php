<?php namespace Jakten\Providers;

use Jakten\Repositories\Contracts\OrderRepositoryContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewInstance;
use Jakten\Facades\Auth;
use Jakten\Models\Page;
use Jakten\Repositories\Contracts\UserRepositoryContract;

/**
 * Class ComposerServiceProvider
 *
 * @package Jakten\Providers
 */
class ComposerServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        parent::boot();

        View::composer('shared::layouts.master', function (ViewInstance $view) {
            if ($user = Auth::user()) {
                $view->with('user', $user);
            }
        });

        View::composer('shared::components.footer', function (ViewInstance $view) {
            $view->with('pages', Page::get());
        });

        View::composer('organization::layouts.default', function (ViewInstance $view) {
            $organization = Auth::user()->organization;
            $unhandledOrders = $this->app->make(OrderRepositoryContract::class)
                ->forOrganization($organization)
                ->query()->where('handled', false)
                ->where('cancelled', false)
                ->count();

            $schoolCount = $organization->schools->count();

            $view->with('unhandledOrders', $unhandledOrders);
            $view->with('schoolCount', $schoolCount);
        });

        View::composer('mail::message', function (ViewInstance $view) {
            if ($view->email) {
                $user = $this->app->make(UserRepositoryContract::class)
                    ->query()->where('email', $view->email)->first();

                if ($user) {
                    $view->with('isStudent', $user->isStudent());
                }
            }
        });
    }
}
