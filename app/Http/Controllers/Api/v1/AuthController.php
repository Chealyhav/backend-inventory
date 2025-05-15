<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\v1\BaseAPI;
use App\Services\AuthSV;

class AuthController extends BaseAPI
{
    protected $authService;
    public function __construct()
    {
        $this->authService = new AuthSV();
    }
    public function login(Request $request)
    {
        try {
            $data = $this->authService->UserLogin($request->all());
            return $this->successResponse($data, 'User login successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
    public function register(Request $request)
    {
        try {
            $data = $this->authService->UserCreate($request->all());
            return $this->successResponse($data, 'User register successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
    // Refresh token
    // Refresh JWT token
    public function refreshToken(Request $request)
    {
        try {
            $data = $this->authService->refresh();
            return $this->successResponse($data, 'Refresh token successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
    // Logout user
    public function logout(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $this->authService->logout($token);
            return $this->successResponse( 'User logout successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
    // Reset password
    public function resetPassword($token, $password, Request $request)
    {
        try {
            $params = $request->all();
            $params['token'] = $token;
            $params['password'] = $password;
            $data = $this->authService->resetPassword($params, $password);
            return $this->successResponse($data, 'Password reset successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }


    // Get user profile
    public function getProfile(Request $request)
    {
        try {
            // Ensure the token is set in the Authorization header
            $data = $this->authService->getProfile();

            return $this->successResponse($data, 'Profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
}
