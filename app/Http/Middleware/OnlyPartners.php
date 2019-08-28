<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\TokenGuard;
use Illuminate\Http\Request;

class OnlyPartners
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
        if (\Auth::guest()) {
            return redirect(routePartner($request->partner, 'admin.login'));
        }

        $user = \Auth::user();

        if (!$user->partner) {
            return $this->denyAccess($request);
        }

        return $next($request);
    }

    /**
     * Deny access to protected routes.
     * Depending on Guard type this method will return either JSON response or redirect response.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function denyAccess(Request $request)
    {
        $guard = \Auth::getFacadeRoot()->guard();

        // Here we're checking for which Guard we're working with. Since TokenGuard guards
        // are stateless and does not provide `logout` method, API requests are failing
        // with 'Undefined method' exceptions if current user does not have a partner.

        // Instead of logging user out we will return JSON
        // response with "Unauthenticated" error, so API
        // consumers won't receive HTTP 500 responses.

        if ($guard instanceof TokenGuard) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $guard->logout();

        return redirect(route('admin.login'));
    }
}
