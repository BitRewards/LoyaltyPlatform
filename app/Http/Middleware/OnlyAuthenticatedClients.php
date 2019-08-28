<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OnlyAuthenticatedClients
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
        if (!\Auth::user()) {
            if (RouteServiceProvider::isRESTApiRequest($request)) {
                throw new HttpException(Response::HTTP_UNAUTHORIZED);
            }

            return redirect(routePartner($request->partner, 'client.index'));
        }

        return $next($request);
    }
}
