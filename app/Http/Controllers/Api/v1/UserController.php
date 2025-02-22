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
    public function index(Request $request)
    {
        try {
            $data = $this->user->getUserList($request->all());
            return $this->successResponse($data, 'User list successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
    public function show($id)
    {
        try {
            if (empty($id)) {
                throw new \Exception('User id is required');
            }
            $data = $this->user->getUserById($id);
            return $this->successResponse($data, 'User detail successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
    public function store(Request $request)
    {
        try {
            $data = $this->user->createUser($request->all());
            return $this->successResponse($data, 'User created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $data = $this->user->updateUser($request->all(), $id);
            return $this->successResponse($data, 'User updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $data = $this->user->deleteUser($id);
            return $this->successResponse($data, 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
