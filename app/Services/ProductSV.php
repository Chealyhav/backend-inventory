<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductSV extends BaseService
{
    public function getQuery()
    {
        return Product::query();
    }

    // Get all products
    public function getAllProducts($params = [])
    {
        $query = $this->getQuery()
            ->join('sub_category', 'sub_category.id', '=', 'products.sub_category_id')
            ->select(
                'products.id',
                'products.product_name',
                'products.product_code',
                'products.img_url',
                'products.sub_category_id',
                'products.availableStock',
                'products.stockType',
                'products.status',
                'products.created_at',
                'products.updated_at',
                'products.created_by',
                'products.updated_by',
                'products.deleted_at',
                'products.deleted_by',
                'sub_category.name as sub_category_name'
            );

        // Filter by search term
        if (isset($params['search'])) {
            $query->where('product_name', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('product_code', 'LIKE', '%' . $params['search'] . '%');
        }

        // Pagination


        if (isset($params['order_by'])) {
            $orderBy = $params['order_by'];
            $order = $params['order'] ?? 'asc';
            $query->orderBy($orderBy, $order);
        }

        // Apply custom filters if provided
        if (isset($params['filter_by'])) {
            foreach ($params['filter_by'] as $column => $value) {
                $query->where($column, $value);
            }
        }

        // Pagination setup
        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        // Apply ordering
        $orderBy = $params['order_by'] ?? 'created_at';
        $order = $params['order'] ?? 'asc';
        $query->orderBy($orderBy, $order);

        // Count total records for pagination
        $total = $query->count();

        // Apply pagination (limit and offset)
        $query->skip($offset)->take($limit);


        $result = $query->get();
        $data = $result->map(function ($product) {
            return [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'product_code' => $product->product_code,
                'img_url' => $product->img_url,
                'sub_category_id' => $product->sub_category_id,
                'availableStock' => $product->availableStock,
                'stockType' => $product->stockType,
                'status' => $product->status,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
                'created_by' => $product->created_by,
                'updated_by' => $product->updated_by,
                'deleted_at' => $product->deleted_at,
                'deleted_by' => $product->deleted_by,
                'sub_category_name' => $product->sub_category_name,
            ];
        });

        return $data;
    }


    // Get product by ID
    public function getProductById($id)
    {
        $product = $this->getQuery()->
            join('sub_category', 'sub_category.id', '=', 'products.sub_category_id')
            ->select(
                'products.id',
                'products.product_name',
                'products.product_code',
                'products.img_url',
                'products.sub_category_id',
                'products.availableStock',
                'products.stockType',
                'products.status',
                'products.created_at',
                'products.updated_at',
                'products.created_by',
                'products.updated_by',
                'products.deleted_at',
                'products.deleted_by',
                'sub_category.name as sub_category_name'
            )
            ->find($id);

        if (!$product) {
            throw new ModelNotFoundException('Product not found.');
        }

        return $product;
    }

    // Create a product
    public function createProduct(array $params)
    {
        $params['status'] = $params['status'] ?? 1;
        $product = $this->getQuery()->create($params);

        if (!$product) {
            throw new Exception('Product creation failed.');
        }

        return $product;
    }

    // Update a product
    public function updateProduct($id, array $params)
    {
        $product = $this->getQuery()->find($id);

        if (!$product) {
            throw new ModelNotFoundException('Product not found.');
        }

        $product->update($params);

        return $product;
    }

    // Soft delete a product
    public function deleteProduct($id)
    {
        $product = $this->getQuery()->find($id);

        if (!$product) {
            throw new ModelNotFoundException('Product not found.');
        }

        $product->update(['status' => 0]);

        return $product->delete();
    }

    // Restore a soft-deleted product
    public function restoreProduct($id)
    {
        $product = $this->getQuery()->withTrashed()->find($id);

        if (!$product) {
            throw new ModelNotFoundException('Product not found or not archived.');
        }

        $product->update(['status' => 1]);

        return $product->restore();
    }

    // Permanently delete a product from the database
    public function deleteProductFromDb($id)
    {
        $product = $this->getQuery()->withTrashed()->find($id);

        if (!$product) {
            throw new ModelNotFoundException('Product not found.');
        }

        return $product->forceDelete();
    }
}
