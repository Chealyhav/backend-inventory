<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        // Token has expired
        if ($e instanceof TokenExpiredException) {
            return response()->json(['message' => 'Token has expired'], 401);
        }

        // Token is invalid
        if ($e instanceof TokenInvalidException) {
            return response()->json(['message' => 'Token is invalid'], 401);
        }

        // Token is missing or not provided
        if ($e instanceof JWTException) {
            return response()->json(['message' => 'Token is missing or not provided'], 401);
        }

        // Fallback unauthorized error
        if ($e instanceof UnauthorizedHttpException) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Let Laravel handle all other exceptions
        return parent::render($request, $e);
    }
}
