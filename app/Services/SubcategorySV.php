<?php

namespace App\Services;

use App\Models\Subcategory;
use App\Models\Category;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubcategorySV extends BaseService
{
    public function getQuery()
    {
        return Subcategory::query();
    }


    // Additional methods for Subcategory service

    //get all category
    public function getAllSubcategories($params = [])
    {
        // Build the query
        $query = $this->getQuery()->with('category')
            ->select('id', 'name', 'category_id', 'status', 'updated_at', 'created_at', 'created_by', 'updated_by', 'deleted_at', 'deleted_by');

        // Filter by search term
        if (isset($params['search'])) {
            $query->where('name', 'LIKE', '%' . $params['search'] . '%');
        }

        // Pagination setup
        $limit = $params['limit'] ?? 10;  // Default to 10 items per page
        $page = $params['page'] ?? 1;  // Default to the first page
        $offset = ($page - 1) * $limit;  // Calculate the offset based on the page number

        // Count total records for pagination
        $total = $query->count();

        // Apply pagination (limit and offset)
        $query->skip($offset)->take($limit);

        // Execute the query and get the data
        $data = $query->get();

        // Transform the data to include category name
        $result = $data->map(function ($subcategory) {
            return [
                'id' => $subcategory->id,
                'name' => $subcategory->name,
                'category_id' => $subcategory->category_id,
                'status' => $subcategory->status,
                'categoryname' => $subcategory->category ? $subcategory->category->name : null,
                'created_at' => $subcategory->created_at,
                'updated_at' => $subcategory->updated_at,
                'created_by' => $subcategory->created_by,
                'updated_by' => $subcategory->updated_by,
                'deleted_at' => $subcategory->deleted_at,
                'deleted_by' => $subcategory->deleted_by,
            ];
        });

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
            'data' => $result,
        ];
    }


    //get subcategory by id
    public function getSubcategoryById($id)
    {
        $query = $this->getQuery();
        $query->where('id', $id);
        $query->with('category');
        if (isset($query)) {
            $data = $query->first();
            return $data;
        } else {
            throw new Exception('Query not found');
        }
    }
    //create subcategory
    public function createSubcategory(array $params = array())
    {
        $query = $this->getQuery();
        $params['status'] = $params['status'] ?? 1;
        if (isset($query)) {
            $data = $query->create($params);
            return $data;
        } else {
            throw new Exception('Query not found');
        }
    }
    //update subcategory
    public function updateSubcategory($id, array $params = array())
    {
        $query = $this->getQuery();
        if (isset($query)) {
            $data = $query->where('id', $id)->update($params);
            //return $data update data
            $data = $query->where('id', $id)->first();
            return $data;
        } else {
            throw new Exception('Query not found');
        }
    }
    //delete subcategory
    public function deleteSubcategory($id)
    {
        $query = $this->getQuery();
        if (isset($query)) {
            //soft delete
            $data = $query->where('id', $id)->first();
            $data = $query->where('id', $id)->delete();
            //return $data delete data
            $data = $query->where('id', $id)->update(['status' => 0]);
            return $data;
        } else {
            throw new Exception('Query not found');
        }
    }

    // Restore an archived subcategory
    public function restoreSubcategory($id)
    {
        $subcategory = $this->getQuery()->withTrashed()->find($id);



        if (!$subcategory) {
            throw new ModelNotFoundException('Subcategory not found or not archived.');
        }
        $subcategory->update(['status' => 1]);

        return $subcategory->restore();
    }

    //delete subcategory  from database
    public function  deleteSubcategoryDb($id)
    {
        $subcategory = $this->getQuery()->find($id);

        if (!$subcategory) {
            throw new ModelNotFoundException('Subcategory not found or not archived.');
        }

        return $subcategory->delete();
    }
}
