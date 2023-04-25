<?php

namespace Shared\Http\Middleware;

use Closure;

class EnableIframeOptions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        //todo make based on school id
        $allowedWebsites = [
            'https://staging.korkortsjakten.se',
            'https://korkortsjakten.se',
            'https://www.solnatrafikskola.nu/',
            'https://solnatrafikskola.nu/',
            'https://classestrafikskola.se/',
            'https://www.classestrafikskola.se/',
            'https://forssa-trafikskola.se/',
            'https://www.forssa-trafikskola.se/',
        ];
        $allowedWebsites = implode(' ', $allowedWebsites);

        $response->headers->set('Content-Security-Policy', 'frame-ancestors ' . $allowedWebsites);

        return $response;
    }
}
