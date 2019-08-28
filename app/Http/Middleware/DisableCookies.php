<?php

namespace App\Http\Middleware;

class DisableCookies
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        \Config::set('session.driver', 'array');
        \Config::set('cookie.driver', 'array');

        return $next($request);
    }
}
