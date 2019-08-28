<?php

namespace App\Http\Middleware;

use App\Services\UserService;
use Closure;

class OnlyConfirmedClients
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->partner && app(UserService::class)->isUserConfirmed(\Auth::user())) {
            return $next($request);
        } else {
            return redirect(routePartner($request->partner, 'client.index'));
        }
    }
}
