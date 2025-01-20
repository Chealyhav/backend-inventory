<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseAPI;
use App\Services\RoleSV;
use App\Http\Requests\StoreRoleRequest;  // Import the StoreRoleRequest
use App\Http\Requests\UpdateRoleRequest; // Import the UpdateRoleRequest
use Illuminate\Http\Request;

class RoleController extends BaseAPI
{
    protected $roleSV;

    public function __construct()
    {
        $this->roleSV = new RoleSV();
    }

    // Get all roles
    public function index(Request $request)
    {
        try {
            $params = $request->all();
            $roles = $this->roleSV->RoleList($params);
            return $this->sendResponse($roles, 'Roles retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // Create a new role
    public function store(StoreRoleRequest $request)
    {
        try {
            $param['name'] = $request->input('name');
            $param['status'] = $request->input('status');
            $param['created_by'] = $request->input('created_by');
            $role = $this->roleSV->RoleCreate($param);

            return $this->sendResponse($role, 'Role created successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // Update an existing role
    public function update($id, UpdateRoleRequest $request)
    {
        try {
            $param['name'] = $request->input('name');
            $param['status'] = $request->input('status');
            $param['updated_by'] = $request->input('updated_by');

            $role = $this->roleSV->RoleUpdate($id, $param);

            return $this->sendResponse($role, 'Role updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // Delete a role
    public function destroy($id)
    {
        try {
            $role = $this->roleSV->RoleDelete($id);
            return $this->sendResponse($role, 'Role deleted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // Show details of a single role
    public function show($id)
    {
        try {
            $role = $this->roleSV->RoleDetail($id);
            return $this->sendResponse($role, 'Role details retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
