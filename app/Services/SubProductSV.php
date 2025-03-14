<?php

namespace App\Services;

use App\Models\Subproduct;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubProductSV extends BaseService
{
    // Get base query
    public function getQuery()
    {
        return Subproduct::query();
    }

    // Get all subproducts with optional filters and pagination
    public function getAllSubProducts($params = [])
    {
        $query = $this->getQuery()
            ->join('products', 'products.id', '=', 'subproducts.product_id')
            ->join('colors', 'colors.id', '=', 'subproducts.color_id')

            ->leftJoin('sub_category', 'sub_category.id', '=', 'products.sub_category_id')
            ->leftJoin('categories', 'categories.id', '=', 'sub_category.category_id')
            ->select(
                'subproducts.id',
                'subproducts.code',
                'subproducts.pieces',
                'subproducts.thickness',
                'subproducts.length',
                'subproducts.unit_weight',
                'subproducts.total_weight',
                'subproducts.sale_price',
                'subproducts.buy_price',
                'subproducts.discount',
                'products.stockType',
                'subproducts.status',
                'subproducts.created_at',
                'subproducts.updated_at',
                'subproducts.created_by',
                'subproducts.updated_by',
                'subproducts.deleted_at',
                'subproducts.deleted_by',
                'products.product_name',
                'colors.name as color_name',
                'categories.name as category_name',
                'sub_category.name as sub_category_name',
                'products.id as product_id',
            );


        // Filter by search term
        if (isset($params['search'])) {
            $query->where('subproducts.code', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('products.product_name', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('colors.name', 'LIKE', '%' . $params['search'] . '%');
        }

        // Filter by product_id
        if (isset($params['product_id'])) {
            $query->where('subproducts.product_id', $params['product_id']);
        }
        // Filter by categories
        if (isset($params['category_id'])) {
            $query->where('categories.id', $params['category_id']);
        }
        // Filter by sub_category_id
        if (isset($params['sub_category_id'])) {
            $query->where('sub_category.id', $params['sub_category_id']);
        }



        // Pagination setup
        // $limit = $params['limit'] ?? 10;
        // $page = $params['page'] ?? 1;
        // $offset = ($page - 1) * $limit;

        // Apply ordering
        // $orderBy = $params['order_by'] ?? 'subproducts.created_at';
        // $order = $params['order'] ?? 'asc';
        // $query->orderBy($orderBy, $order);

        // Count total records for pagination
        $total = $query->count();


        $result = $query->get();
        if (!$result) {
            throw new Exception('SubProduct not found.');
        }
        $data = $result->map(function ($subProduct) {
            return [

                'code' => $subProduct->code,
                'pieces' => $subProduct->pieces,
                'thickness' => $subProduct->thickness,
                'length' => $subProduct->length,
                'unit_weight' => $subProduct->unit_weight,
                'total_weight' => $subProduct->total_weight,
                'sale_price' => $subProduct->sale_price,
                'buy_price' => $subProduct->buy_price,
                'discount' => $subProduct->discount,
                'stockType' => $subProduct->stockType,
                'status' => $subProduct->status,
                'created_at' => $subProduct->created_at,
                'updated_at' => $subProduct->updated_at,
                'created_by' => $subProduct->created_by,
                'updated_by' => $subProduct->updated_by,
                'deleted_at' => $subProduct->deleted_at,
                'deleted_by' => $subProduct->deleted_by,
                'product_name' => $subProduct->product_name,
                'product_id' => $subProduct->product_id,
                'color_name' => $subProduct->color_name ?? '',
                'category_name' => $subProduct->category_name,
                'sub_category_name' => $subProduct->sub_category_name,
                'id' => $subProduct->id,
            ];
        });

        return $data;
    }

    // Get subproduct by ID
    public function getSubProductById($id, $params = [])
    {
        if (empty($id)) {
            throw new Exception('SubProduct ID is required.');
        }
        $subProduct = $this->getQuery()->find($id);

        if (!$subProduct) {
            throw new ModelNotFoundException('SubProduct not found.');
        }
        // Update the product with the new parameters
        $subProduct->update($params);

        return $subProduct;
    }


    // Create a subproduct
    public function createSubProduct(array $params)
    {
        if (empty($params['product_id'])) {
            throw new Exception('Product ID is required.');
        }

        // if (empty($params['color_id'])) {
        //     throw new Exception('Color ID is required.');
        // }
        $params['status'] = $params['status'] ?? 1;
        $subProduct = $this->getQuery()->create($params);

        if (!$subProduct) {
            throw new Exception('SubProduct creation failed.');
        }

        return $subProduct;
    }

    // Update a subproduct
    public function updateSubProduct($id, array $params)
    {
        if (empty($params['product_id'])) {
            throw new Exception('Product ID is required.');
        }
        if (empty($params['color_id'])) {
            throw new Exception('Color ID is required.');
        }
        if (empty($params['id'])) {
            throw new Exception('SubProduct ID is required.');
        }
        $params['status'] = $params['status'] ?? 1;
        $subProduct = $this->getQuery()->find($id);

        if (!$subProduct) {
            throw new ModelNotFoundException('SubProduct not found.');
        }

        $subProduct->update($params);

        return $subProduct;
    }

    // Soft delete a subproduct
    public function deleteSubProduct($id)
    {
        if (empty($id)) {
            throw new Exception('SubProduct ID is required.');
        }
        $subProduct = $this->getQuery()->find($id);

        if (!$subProduct) {
            throw new ModelNotFoundException('SubProduct not found.');
        }

        $subProduct->update(['status' => 0]);

        return $subProduct->delete();
    }

    // Restore a soft-deleted subproduct
    public function restoreSubProduct($id)
    {
        if (empty($id)) {
            throw new Exception('SubProduct ID is required.');
        }
        $subProduct = $this->getQuery()->withTrashed()->find($id);

        if (!$subProduct) {
            throw new ModelNotFoundException('SubProduct not found or not archived.');
        }

        $subProduct->update(['status' => 1]);

        return $subProduct->restore();
    }

    // Permanently delete a subproduct from the database
    public function deleteSubProductFromDb($id)
    {
        if (empty($id)) {
            throw new Exception('SubProduct ID is required.');
        }
        $subProduct = $this->getQuery()->withTrashed()->find($id);

        if (!$subProduct) {
            throw new ModelNotFoundException('SubProduct not found.');
        }

        return $subProduct->forceDelete();
    }
    //delete product
    public function deleteProductFromDb($id)
    {
        $query = $this->getQuery();

        if (isset($query)) {
            $data = $query->where('id', $id)->first();
            if (isset($data)) {
                $data->delete();
                return $data;
            } else {
                throw new Exception("Record " . $id . " not found in model " . $query->getModel()::class . "");
            }
        } else {
            throw new Exception('Query not found');
        }
    }


    public function SubProductDelete($id)
    {

        $query = $this->getQuery();
        $subProduct = $query->find($id);

        if ($subProduct) {
            $subProduct->delete();
            return $subProduct;
        } else {
            throw new Exception("SubProduct with ID {$id} not found.");
        }
    }
}
