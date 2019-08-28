<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Closure;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    /*public function handle($request, Closure $next)
    {
//        // Это партнер comf.ru - они что-то поломали на своей стороне и отдают нам кривые csrf токены
//        if (false && $request->partner && 'Z8snW45Aqf' === $request->partner) {
//            return tap($next($request), function ($response) use ($request) {
//                if ($this->shouldAddXsrfTokenCookie()) {
//                    $this->addCookieToResponse($request, $response);
//                }
//            });
//        }

        return parent::handle($request, $next);
    }*/

    protected function shouldPassThrough($request)
    {
        if (!$request->user()) {
            return true;
        }

        return parent::shouldPassThrough($request);
    }
}
