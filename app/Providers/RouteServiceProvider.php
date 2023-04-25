<?php namespace Jakten\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * Class RouteServiceProvider
 * @package Jakten\Providers
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $adminNamespace = 'Admin\Http\Controllers';
    protected $organizationNamespace = 'Organization\Http\Controllers';
    protected $studentNamespace = 'Student\Http\Controllers';
    protected $sharedNamespace = 'Shared\Http\Controllers';
    protected $apiNamespace = 'Api\Http\Controllers';
    protected $publicApiNamespace = 'PublicApi\Http\Controllers';
    protected $blogNamespace = 'Blog\Http\Controllers';
    protected $telegramNamespace = 'TelegramBot\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapAdminRoutes();
        $this->mapOrganizationRoutes();
        $this->mapStudentRoutes();
        $this->mapApiRoutes();
        $this->mapPublicApiRoutes();
        $this->mapBlogRoutes();
        $this->mapSharedRoutes();
        $this->mapTelegramRoutes();
    }

    /**
     * mapAdminRoutes
     */
    private function mapAdminRoutes()
    {
        Route::group([
            'namespace' => $this->adminNamespace,
            'prefix' => 'admin',
            'middleware' => ['web', 'auth.admin'],
        ], function ($router) {
            require app_path('Modules/Admin/Http/routes.php');
        });
    }

    /**
     * mapOrganizationRoutes
     */
    private function mapOrganizationRoutes()
    {
        Route::group([
            'namespace' => $this->organizationNamespace,
            'prefix' => 'organization',
            'middleware' => ['web', 'auth.organization'],
        ], function ($router) {
            require app_path('Modules/Organization/Http/routes.php');
        });
    }

    /**
     * mapStudentRoutes
     */
    private function mapStudentRoutes()
    {
        Route::group([
            'namespace' => $this->studentNamespace,
            'prefix' => 'student',
            'middleware' => ['web', 'auth.student'],
        ], function ($router) {
            require app_path('Modules/Student/Http/routes.php');
        });
    }

    /**
     * mapSharedRoutes
     */
    private function mapSharedRoutes()
    {
        Route::group([
            'namespace' => $this->sharedNamespace,
            'middleware' => ['web'],
            'laroute' => true,
        ], function ($router) {
            require app_path('Modules/Shared/Http/routes.php');
        });
    }

    /**
     * mapApiRoutes
     */
    private function mapApiRoutes()
    {
        Route::group([
            'prefix' => 'api',
            'namespace' => $this->apiNamespace,
            'middleware' => ['web', 'api'],
            'laroute' => true,
        ], function ($router) {
            require app_path('Modules/Api/Http/routes.php');
        });
    }

    /**
     * mapPublicApiRoutes
     */
    private function mapPublicApiRoutes()
    {
        Route::group([
            'prefix' => 'v1/api',
            'namespace' => $this->publicApiNamespace,
            'middleware' => ['api'],
            'laroute' => true,
        ], function ($router) {
            require app_path('Modules/PublicApi/Http/routes.php');
        });
    }

    /**
     * mapBlogRoutes
     */
    private function mapBlogRoutes()
    {
        Route::group([
            'prefix' => 'blog',
            'namespace' => $this->blogNamespace,
            'middleware' => ['web'],
            'laroute' => true,
        ], function ($router) {
            require app_path('Modules/Blog/Http/routes.php');
        });
    }

    /**
     * mapTelegramRoutes
     */
    private function mapTelegramRoutes()
    {
        Route::group([
            'prefix' => 'telegram',
            'namespace' => $this->telegramNamespace,
        ], function ($router) {
            require app_path('Modules/TelegramBot/Http/routes.php');
        });
    }
}
