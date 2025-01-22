<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

class RoleSV extends BaseService
{
    public function getQuery()
    {
        return Role::query();
    }

    public function RoleList($params = array())
    {
        $query = $this->getQuery();
        if (isset($params['search'])) {
            $query->where('name', 'LIKE', '%' . $params['search'] . '%');
        }
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }
        $query->orderBy('created_at', 'desc');
        return $query->get();
    }

    public function RoleCreate(array $params = array())
    {
        $query = $this->getQuery();
        return $query->create([
            'name' => $params['name'],
            'status' => $params['status'],
            //'created_by' => Auth::user()->id, // assuming you're using Auth
        ]);
    }

    public function RoleUpdate($id, array $params = array())
    {
        $query = $this->getQuery();
        $role = $query->find($id);

        if ($role) {
            $role->update([
                'name' => $params['name'],
                'status' => $params['status'],
                //'updated_by' => Auth::user()->id,
            ]);
            return $role;
        }

        throw new Exception("Role with ID {$id} not found.");
    }

    public function RoleDelete($id)
    {
        $query = $this->getQuery();
        $role = $query->find($id);

        if ($role) {
            $role->delete();
            return $role;
        }

        throw new Exception("Role with ID {$id} not found.");
    }

    public function RoleDetail($id)
    {
        $query = $this->getQuery();
        $role = $query->find($id);

        if ($role) {
            return $role;
        }

        throw new Exception("Role with ID {$id} not found.");
    }
}
