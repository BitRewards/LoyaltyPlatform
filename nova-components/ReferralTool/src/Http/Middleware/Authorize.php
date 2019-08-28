<?php

namespace Bitrewards\ReferralTool\Http\Middleware;

use Bitrewards\ReferralTool\ReferralTool;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        return resolve(ReferralTool::class)->authorize($request) ? $next($request) : abort(403);
    }
}
