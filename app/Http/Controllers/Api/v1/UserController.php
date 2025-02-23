<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\v1\BaseAPI;
use App\Services\UserSV;

class UserController extends BaseAPI
{
    protected $user;
    public function __construct(UserSV $user)
    {
        $this->user = $user;
    }
    public function index( Request $request)
    {
        try {
            $params = $request->all();
            $data = $this->user->getAllUsers($params);
            return $this->successResponse($data, 'User list');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
    public function store(Request $request)
    {
        try {
            $data = $this->user->UserCreate($request->all());
            return $this->successResponse($data, 'User created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
    //show user
    public function show($id)
    {
        try {
            $data = $this->user->getUserById($id);
            return $this->successResponse($data, 'User details');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
    //update user
    public function update(Request $request, $id)
    {
        try {
            $data = $this->user->UserUpdate($id, $request->all());
            return $this->successResponse($data, 'User updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
    //delete user
    public function destroy($id)
    {
        try {
            $data = $this->user->UserDelete($id);
            return $this->successResponse($data, 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
}
