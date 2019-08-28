<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/29/18
 * Time: 5:18 PM.
 */

namespace App\Http\Middleware;

class AdministratorAuthentication
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
        \Auth::shouldUse('admin');

        return $next($request);
    }
}
