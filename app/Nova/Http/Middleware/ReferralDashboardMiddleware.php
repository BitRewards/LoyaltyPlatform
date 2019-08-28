<?php

namespace App\Nova\Http\Middleware;

use Bitrewards\ReferralTool\ReferralTool;
use Closure;
use Illuminate\Http\Request;

class ReferralDashboardMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!ReferralTool::isEnabled($request)) {
            return response('', 403);
        }

        return $next($request);
    }
}
