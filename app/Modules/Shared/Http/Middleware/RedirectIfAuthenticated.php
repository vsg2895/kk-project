<?php namespace Shared\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Jakten\Helpers\Roles;

/**
 * Class RedirectIfAuthenticated
 * @package Shared\Http\Middleware
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return redirect(Roles::getDashboardRouteForUser(Auth::user()));
        }

        return $next($request);
    }
}
