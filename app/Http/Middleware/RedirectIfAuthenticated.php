<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Check if current request is for admin routes
                if ($guard === 'admin' && $request->is('admin/*')) {
                    return redirect()->route('admin.auth.dashboard');
                } elseif ($guard === 'web' && !$request->is('admin/*')) {
                    return redirect('/user'); // Redirect to user dashboard URL path
                }
                // If user is authenticated as web but accessing admin routes, allow access to admin login
                // If user is authenticated as admin but accessing non-admin routes, allow access
            }
        }

        return $next($request);
    }
}
