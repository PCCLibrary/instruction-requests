<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CasAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated using the CAS guard
        if (!Auth::guard('cas')->check()) {
            // Trigger CAS authentication
            Auth::guard('cas')->authenticate();
        }

        // User is authenticated, proceed with the request
        return $next($request);
    }
}
