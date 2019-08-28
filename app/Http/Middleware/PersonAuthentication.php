<?php
/**
 * PersonAuthentication.php
 * Creator: lehadnk
 * Date: 13/08/2018.
 */

namespace App\Http\Middleware;

class PersonAuthentication
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
        if (null !== \Auth::guard('person')->user()) {
            \Auth::shouldUse('person');
        }

        return $next($request);
    }
}
