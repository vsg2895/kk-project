<?php namespace Jakten\Helpers\BladeSvgIcon;

use Illuminate\Support\ServiceProvider;

/**
 * Class BladeSvgIconServiceProvider
 * @package Jakten\Helpers\BladeSvgIcon
 */
class BladeSvgIconServiceProvider extends ServiceProvider
{
    /**
     * Boot
     */
    public function boot()
    {
        app(IconFactory::class)->registerBladeTag();
    }

    /**
     * Register
     */
    public function register()
    {
        require_once base_path() . '/app/Helpers/BladeSvgIcon/Functions.php';

        $this->app->singleton(IconFactory::class, function () {
            $config = array_merge(config('blade-svg', []), [
                'spritesheet_path' => null,
                'icon_path' => base_path('resources/assets/svg/icons'),
            ]);

            return new IconFactory($config);
        });
    }
}
