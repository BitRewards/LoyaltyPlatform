<?php

namespace App\Nova\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DefaultPageMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ('nova.index' === $request->route()->getName()) {
            return redirect('/dashboard/tools-statistic');
        }

        return $next($request);
    }
}
