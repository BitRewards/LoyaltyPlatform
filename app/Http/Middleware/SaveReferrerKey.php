<?php

namespace App\Http\Middleware;

use App\Services\UserService;
use Closure;

class SaveReferrerKey
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
        $referrerKey = $request->input(UserService::USER_REFERRER_KEY_CLIENT_PARAM);

        if (!is_null($referrerKey)) {
            session()->put(UserService::USER_REFERRER_KEY_SESSION_KEY, $referrerKey);
        }

        return $next($request);
    }
}
