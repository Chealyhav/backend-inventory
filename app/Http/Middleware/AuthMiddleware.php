<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use function Illuminate\Log\log;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Check if the user is authenticated with JWT
            if (!JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token is invalid or expired.'], 401);
        }

        return $next($request);
    }
}

