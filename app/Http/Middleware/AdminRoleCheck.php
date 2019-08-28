<?php

namespace App\Http\Middleware;

use Closure;

class AdminRoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $user = \Auth::guard()->user();

        // Guests
        if (!$user) {
            return redirect(route('admin.login'));
        }

        // Partner's user
        if (empty($user->role)) {
            \Auth::logout();

            return redirect(route('admin.login'));
        }

        // Partners & admins
        if (!$user->can($role)) {
            return redirect('/admin');
        }

        return $next($request);
    }
}
