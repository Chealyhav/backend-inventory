<?php

namespace App\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use App\Models\Category;

class CategorySV extends BaseService
{
    function getQuery()
    {
        return Category::query();
    }
    public function CategoryList($params = array())
    {
        $query = $this->getQuery();
        if(isset($params['search'])){
            $query->where('name', 'LIKE', '%'.$params['search'].'%');
        }
        if(isset($params['status'])){
            $query->where('status', $params['status']);
        }
        //order by  created_at desc
        $query->orderBy('created_at', 'desc');
        if(isset($query)){
            $data = $query->get();
            return $data;
        } else {
            throw new Exception('Query not found');
        }
    }
    public function CategoryCreate($params = array())
    {
        $query = $this->getQuery();
        if(isset($query)){
            $data = $query->create($params);
            return $data;
        } else {
            throw new Exception('Query not found');
        }
    }
    public function CategoryUpdate($id, array $params = array())
    {
        $query = $this->getQuery();
        if(isset($query)){
            $data = $query->where('id', $id)->update($params);
            return $data;
        } else {
            throw new Exception('Query not found');
        }
    }
    public function CategoryDelete($id)
    {
        $query = $this->getQuery();
        if(isset($query)){
            $data = $query->where('id', $id)->delete();
            return $data;
        } else {
            throw new Exception('Query not found');
        }
    }
    public function CategoryDetail($id)
    {
        $query = $this->getQuery();
        if(isset($query)){
            $data = $query->where('id', $id)->first();
            return $data;
        } else {
            throw new Exception('Query not found');
        }
    }
}
