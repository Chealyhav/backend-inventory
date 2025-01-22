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


    public function ColorCreate(array $params = array())
    {
        $query = $this->getQuery();
        if(isset($query)){
            $data = $query->create(
                [
                    'name' => $params['name'] ?? null,
                    'code' => $params['code'] ?? null,
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
                        'code' => $params['code'] ?? null,
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
