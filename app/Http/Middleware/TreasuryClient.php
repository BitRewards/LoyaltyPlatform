<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TreasuryClient
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
        $validIp = in_array($request->getClientIp(), config('treasury.allowed_ips'), true);

        if (!$validIp) {
            return $this->denyAccess($request);
        }

        return $next($request);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function denyAccess(Request $request)
    {
        return response()->json(['error' => 'Unauthenticated client '.$request->getClientIp()], 401);
    }
}
