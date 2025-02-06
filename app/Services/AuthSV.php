<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use App\Models\Role;

class AuthSV extends BaseService
{
    public function getQuery()
    {
        return User::query();
    }

    // Login user
    // public function login($request)
    // {
    //     $user = $this->getQuery()->where('email', $request->email)->first();
    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }

    //     if (password_verify($request->password, $user->password)) {
    //         $token = $user->createToken('authToken')->plainTextToken;
    //         return response()->json(['token' => $token], 200);
    //     } else {
    //         return response()->json(['message' => 'Password is incorrect'], 401);
    //     }
    // }

    // // Logout user
    // public function logout()
    // {
    //     Auth::user()->tokens()->delete();
    //     return response()->json(['message' => 'Logout successfully'], 200);
    // }

    // // Refresh token
    // public function refresh()
    // {
    //     try {
    //         $token = Auth::user()->createToken('authToken')->plainTextToken;
    //         return response()->json(['token' => $token], 200);
    //     } catch (TokenExpiredException $e) {
    //         return response()->json(['message' => 'Token is expired'], 401);
    //     }
    // }



}
