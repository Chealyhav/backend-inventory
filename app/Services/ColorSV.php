<?php

namespace App\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use App\Models\Color;


class ColorSV extends BaseService
{

    public function getQuery()
    {
        return Color::query();
    }

    public function ColorList($params = array())
    {

        $query = $this->getQuery();

        // Filter by search term
        if (isset($params['search'])) {
            $query->where('name', 'LIKE', '%' . $params['search'] . '%');
        }

        // Filter by status
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        // Apply ordering by created_at descending
        $query->orderBy('created_at', 'desc');

        // Pagination setup
        $limit = $params['limit'] ?? 10;  // Default to 10 items per page
        $page = $params['page'] ?? 1;  // Default to the first page
        $offset = ($page - 1) * $limit;  // Calculate the offset based on the page number

        // Count total records for pagination
        $total = $query->count();

        // Apply pagination (limit and offset)
        $query->skip($offset)->take($limit);

        // Get the data
        $data = $query->get();

        // Calculate total pages for pagination
        $totalPage = ceil($total / $limit);
        $nextPage = $page + 1;
        $prevPage = $page - 1;

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


    public function ColorCreate(array $params = array())
    {
        $query = $this->getQuery();
        if(isset($query)){
            $data = $query->create(
                [
                    'name' => $params['name'] ?? null,
                    // 'created_by' => Auth::user()->id,

                ]
            );
            return $data;
        } else {
            throw new Exception('Query not found');
        }
    }

    public function ColorUpdate($id, array $params = array())
    {
        $query = $this->getQuery();

        if(isset($query)){
            $data = $query->where('id', $id)->first();
            if(isset($data)){
                $data->update(
                    [
                        'name' => $params['name']?? null,
                        'updated_by' => Auth::user()->id ?? null,
                    ]
                );
                return $data;
            } else {
                throw new Exception("Record ".$id." not found in model ".$query->getModel()::class."");
            }
        } else {
            throw new Exception('Query not found');
        }
    }
    public function ColorDelete($id)
    {
        $query = $this->getQuery();

        if(isset($query)){
            $data = $query->where('id', $id)->first();
            if(isset($data)){
                $data->delete();
                return $data;
            } else {
                throw new Exception("Record ".$id." not found in model ".$query->getModel()::class."");
            }
        } else {
            throw new Exception('Query not found');
        }
    }

    public function ColorDetail($id)
    {
        $query = $this->getQuery();

        if(isset($query)){
            $data = $query->where('id', $id)->first();
            if(isset($data)){
                return $data;
            } else {
                throw new Exception("Record ".$id." not found in model ".$query->getModel()::class."");
            }
        } else {
            throw new Exception('Query not found');
        }
    }
}
