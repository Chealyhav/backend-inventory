<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('api')->check()) {
            return $next($request);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
