<?php namespace Shared\Http;

use Admin\Http\Middleware\AuthAdmin;
use Jakten\Modules\Shared\Http\Middleware\TelegramMiddleware;
use Student\Http\Middleware\AuthStudent;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Middleware\{SubstituteBindings, ThrottleRequests};
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Organization\Http\Middleware\AuthOrganization;
use Illuminate\Auth\Middleware\{Authenticate, AuthenticateWithBasicAuth, Authorize};
use Shared\Http\Middleware\{EnableIframeOptions, EncryptCookies, RedirectIfAuthenticated, VerifyCsrfToken};
use Illuminate\Foundation\Http\Middleware\{CheckForMaintenanceMode, ConvertEmptyStringsToNull, TrimStrings};

/**
 * Class Kernel
 * @package Shared\Http
 */
class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        CheckForMaintenanceMode::class,
        TrimStrings::class,
        ConvertEmptyStringsToNull::class,
        TelegramMiddleware::class
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ],

        'api' => [
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => Authenticate::class,
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'auth.admin' => AuthAdmin::class,
        'auth.organization' => AuthOrganization::class,
        'auth.student' => AuthStudent::class,
        'bindings' => SubstituteBindings::class,
        'can' => Authorize::class,
        'guest' => RedirectIfAuthenticated::class,
        'throttle' => ThrottleRequests::class,
        'allowIframe' => EnableIframeOptions::class,
    ];
}
