<?php

namespace App\Services;

use App\Models\SubProduct;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubProductSV extends BaseService
{
    // Get base query
    public function getQuery()
    {
        return SubProduct::query();
    }

    // Get all subproducts with optional filters and pagination
    public function getAllSubProducts($params = [])
    {
        $query = $this->getQuery()
            ->join('products', 'products.id', '=', 'subproducts.product_id')
            ->join('colors', 'colors.id', '=', 'subproducts.color_id')
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
                'subproducts.status',
                'subproducts.created_at',
                'subproducts.updated_at',
                'subproducts.created_by',
                'subproducts.updated_by',
                'subproducts.deleted_at',
                'subproducts.deleted_by',
                'products.product_name',
                'colors.name as color_name'
            );


        // Filter by search term
        if (isset($params['search'])) {
            $query->where('subproducts.code', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('products.product_name', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('colors.name', 'LIKE', '%' . $params['search'] . '%');
        }

        // Pagination setup
        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        // Apply ordering
        $orderBy = $params['order_by'] ?? 'subproducts.created_at';
        $order = $params['order'] ?? 'asc';
        $query->orderBy($orderBy, $order);

        // Count total records for pagination
        $total = $query->count();

        // Apply pagination (limit and offset)
        $query->skip($offset)->take($limit);

        $result = $query->get();
        if (!$result) {
            throw new Exception('SubProduct not found.');
        }
        $data = $result->map(function ($subProduct) {
            return [
                'id' => $subProduct->id,
                'code' => $subProduct->code,
                'pieces' => $subProduct->pieces,
                'thickness' => $subProduct->thickness,
                'length' => $subProduct->length,
                'unit_weight' => $subProduct->unit_weight,
                'total_weight' => $subProduct->total_weight,
                'sale_price' => $subProduct->sale_price,
                'buy_price' => $subProduct->buy_price,
                'discount' => $subProduct->discount,
                'status' => $subProduct->status,
                'created_at' => $subProduct->created_at,
                'updated_at' => $subProduct->updated_at,
                'created_by' => $subProduct->created_by,
                'updated_by' => $subProduct->updated_by,
                'deleted_at' => $subProduct->deleted_at,
                'deleted_by' => $subProduct->deleted_by,
                'product_name' => $subProduct->product_name,
                'color_name' => $subProduct->color_name,
            ];
        });

        return $data;
    }

    // Get subproduct by ID
    public function getSubProductById($id)
    {
        if (empty($id)) {
            throw new Exception('SubProduct ID is required.');
        }

        $subProduct = $this->getQuery()
            ->where('subproducts.id', $id)
            ->join('products', 'products.id', '=', 'subproducts.product_id')
            ->join('colors', 'colors.id', '=', 'subproducts.color_id')
            ->select(
                'subproducts.id',
                'products.product_name',
                'colors.name as color_name',
                'subproducts.code',
                'subproducts.pieces',
                'subproducts.thickness',
                'subproducts.length',
                'subproducts.unit_weight',
                'subproducts.total_weight',
                'subproducts.sale_price',
                'subproducts.buy_price',
                'subproducts.discount',
                'subproducts.status',
                'subproducts.created_at',
                'subproducts.updated_at',
                'subproducts.created_by',
                'subproducts.updated_by',
                'subproducts.deleted_at',
                'subproducts.deleted_by'
            )
            ->first();  // Use first() since you're fetching a single record


        if (!$subProduct) {
            throw new ModelNotFoundException('SubProduct not found.');
        }

        return $subProduct;
    }


    // Create a subproduct
    public function createSubProduct(array $params)
    {
        if (empty($params['product_id'])) {
            throw new Exception('Product ID is required.');
        }

        if (empty($params['color_id'])) {
            throw new Exception('Color ID is required.');
        }
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
        $params['status'] = $params['status']?? 1;
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





}
