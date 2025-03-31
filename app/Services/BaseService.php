<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Subscription;
use App\Models\User;
use Auth;
use Exception;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;


abstract class BaseService
{
    protected function getLimit(Builder $query, int $limit)
    {
        return isset($limit) && $limit != -1 ? $query->paginate($limit) : $query->get();
    }

    public function create(array $params = array())
    {
        $query = $this->getQuery();

        // generate uuid for global_id field
        //   $params['global_id'] = Str::uuid()->toString();
        // personal information

        if (isset($query)) {
            $data = $query->create($params);
            return $data;
        } else {
            throw new Exception('Query not found');
        }
    }

    //  public function generateUuid()
    //  {
    //      return \Ramsey\Uuid\Uuid::uuid4()->toString();
    //  }

    public function update(array $params = array(), String $id = null)
    {
        $query = $this->getQuery();

        if (isset($query)) {
            $data = $query->where('global_id', $id)->first();
            if (isset($data)) {
                $data->update($params);
                return $data;
            } else {
                throw new Exception("Record " . $id . " not found in model " . $query->getModel()::class . "");
            }
        } else {

            throw new Exception('Query not found');
        }
    }

    public function delete(String $id)
    {
        $query = $this->getQuery();

        if (isset($query)) {
            $data = $query->where('global_id', $id)->first();
            if (isset($data)) {
                $data->delete();
                return $data;
            } else {
                throw new Exception('Data not found');
            }
        } else {
            throw new Exception('Query not found');
        }
    }

    public function getByGlobalId(String $id)
    {
        $query = $this->getQuery();

        if (isset($query)) {
            $data = $query->where('global_id', $id)->first();
            if (isset($data)) {
                return $data;
            } else {
                throw new Exception("Record " . $id . " not found in " . $query->getModel()::class ?? '' . "");
            }
        } else {
            throw new Exception('Query not found');
        }
    }

    public function getAll($params = null)
    {
        $query = $this->getQuery();

        $limit = $params['limit'] ?? 10; // default limit row
        $page = $params['page'] ?? 1; // default page number
        $orderBy = $params['order_by'] ?? 'asc'; 
        $filterBy = $params['filter_by'] ?? null;
        $search = $params['search'] ?? null;
        $columns = $params['columns'] ?? null;

        // Calculate the offset based on the page number and limit
        $offset = ($page - 1) * $limit;

        // Order by created_at desc by default
        if (isset($query)) {
            $query = $query->orderBy('created_at', $orderBy);
        } else {
            throw new Exception('Query not found');
        }

        if (isset($search)) {
            $query = $query->where($columns, 'like', '%' . $search . '%');
        }

        // Apply other filters
        if (isset($filterBy)) {
            foreach ($filterBy as $column => $value) {
                $query = $query->where($column, $value);
            }
        }

        // Count total records without applying limit and offset
        $total = $query->count();

        // Apply limit and offset for pagination
        $query = $query->skip($offset)->take($limit);

        // Retrieve the data for the current page
        $data = $query->get();

        return $data;
    }

    // Get id by role
    public function getRoleId(String $roleType)
    {
        $role = Role::where('role', $roleType)->first();
        $roleId = $role ? $role->id : null;

        return $roleId;
    }

    // Get id by global_id
    public function getIdByGlobalId($modelName, $global_id)
    {
        $model = new $modelName();
        $query = $model->getQuery();

        if (isset($query)) {
            $data = $query->where('global_id', $global_id)->first();

            $dataId = $data ? $data->id : null;
            return $dataId;
        } else {
            throw new Exception('Query not found');
        }
    }

    // Activate and Deactivate a record
    public function activate($global_id, bool $status)
    {
        $query = $this->getQuery();

        if (isset($query)) {
            $data = $query->where('global_id', $global_id)->first();
            $data->update([
                'active' => $status
            ]);
            return $data;
        } else {
            return throw new ModelNotFoundException('Query not found.');
        }
    }



    protected function getQuery()
    {
        return null;
    }
}
