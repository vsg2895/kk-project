<?php namespace Jakten\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\{Blade, Mail, View};
use Rollbar\Rollbar;
use Swift_Plugins_ThrottlerPlugin;

/**
 * Class AppServiceProvider
 * @package Jakten\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::addNamespace('admin', app_path('Modules/Admin/Views'));
        View::addNamespace('student', app_path('Modules/Student/Views'));
        View::addNamespace('organization', app_path('Modules/Organization/Views'));
        View::addNamespace('shared', app_path('Modules/Shared/Views'));
        View::addNamespace('email', app_path('Modules/Email/Views'));
        View::addNamespace('blog', app_path('Modules/Blog/Views'));
        View::addNamespace('telegram', app_path('Modules/TelegramBot/Views'));

        $throttleRate = config('mail.throttleToMessagesPerMin', 5);

        if ($throttleRate) {
            $throttlerPlugin = new Swift_Plugins_ThrottlerPlugin($throttleRate, \Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE);
            Mail::getSwiftMailer()->registerPlugin($throttlerPlugin);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        setlocale(LC_TIME, 'sv_SE.utf8');
        Carbon::setLocale(config('app.lang'));

        // @set variable
        Blade::extend(function ($value) {
            return preg_replace("/@set\(['\"](.*?)['\"]\,(.*)\)/", '<?php $$1 = $2; ?>', $value);
        });

        if ($this->app->environment() !== 'production') {
            $this->registerDev();
        }
    }

    /**
     * registerDev
     */
    public function registerDev()
    {
        $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
    }
}
