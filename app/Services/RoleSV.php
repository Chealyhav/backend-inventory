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

    public function RoleList($params = [])
    {
        $query = $this->getQuery();

        // Apply search filter
        if (!empty($params['search'])) {
            $query->where('name', 'LIKE', '%' . $params['search'] . '%');
        }

        // Apply status filter
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        // Order results by creation date
        $query->orderBy('created_at', 'desc');

        // Pagination logic
        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        $total = $query->count();
        $totalPage = ceil($total / $limit);
        $nextPage = $page < $totalPage ? $page + 1 : null;
        $prevPage = $page > 1 ? $page - 1 : null;

        // Apply pagination (this was missing in your code)
        $data = $query->skip($offset)->take($limit)->get();

        return [
            'total' => $total,
            'totalPage' => $totalPage,
            'nextPage' => $nextPage,
            'prevPage' => $prevPage,
            'currentPage' => $page,
            'limit' => $limit,
            'data' => $data,
        ];
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
