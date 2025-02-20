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


}
