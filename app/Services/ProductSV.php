<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Services\CloudinarySv;

class ProductSV extends BaseService
{
    protected $cloudinarySv;

    // Inject CloudinarySv service into the constructor
    public function __construct(CloudinarySv $cloudinarySv)
    {
        $this->cloudinarySv = $cloudinarySv;
    }

    // Get query builder instance for Product model
    public function getQuery()
    {
        return Product::query();
    }

    // Get all products with pagination and filters
    public function getAllProducts($params = [])
    {
        $query = $this->getQuery()
            ->join('sub_category', 'sub_category.id', '=', 'products.sub_category_id')
            ->join('categories', 'categories.id', '=', 'sub_category.category_id')
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
                'sub_category.name as sub_category_name',
                'categories.name as category_name',
                'categories.id as category_id'
            );

        // Filter by search term
        if (isset($params['search'])) {
            $query->where('product_name', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('product_code', 'LIKE', '%' . $params['search'] . '%');
        }

        // Apply custom filters
        if (isset($params['sub_category_id'])) {
            $query->where('sub_category_id', $params['sub_category_id']);
        }
        if (isset($params['category_id'])) {
            $query->where('categories.id', $params['category_id']);
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

        // Calculate total pages
        $totalPage = ceil($total / $limit);
        $nextPage = $page + 1;
        $prevPage = $page - 1;

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
                'category_name' => $product->category_name,
                'category_id' => $product->category_id,
            ];
        });

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

    // Get a product by ID
    public function getProductById($id)
    {
        $product = $this->getQuery()
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
        // Upload the image to Cloudinary if the file is provided
        if (isset($params['img_url'])) {
            $params['img_url'] = $this->cloudinarySv->uploadImage($params['img_url']);
        }

        $params['status'] = $params['status'] ?? 1;

        try {
            $product = $this->create($params);
            return $product;
        } catch (Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    // Update a product
    public function updateProduct($id, array $params)
    {
        // Find the product by ID
        $product = $this->getQuery()->find($id);

        if (!$product) {
            throw new ModelNotFoundException('Product not found.');
        }

        // If a new image is uploaded and it's different from the current image
        if (isset($params['img_url']) && $params['img_url'] !== $product->img_url) {

            // Ensure the current product has a valid img_url to delete
            if ($product->img_url) {
                // Extract the public_id from the current image URL before deleting
                $currentPublicId = $this->cloudinarySv->extractPublicIdFromUrl($product->img_url);

                // Delete the old image from Cloudinary
                $this->cloudinarySv->deleteImage($currentPublicId);
            }

            // Upload the new image to Cloudinary and get the new image URL
            $params['img_url'] = $this->cloudinarySv->uploadImage($params['img_url']);
        }

        // Update the product with the new parameters
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

        // Delete the image from Cloudinary if the product is being deleted
        if ($product->img_url) {
            $this->cloudinarySv->deleteImage($product->img_url);
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

        // Permanently delete the image from Cloudinary if it's deleted
        if ($product->img_url) {
            $this->cloudinarySv->deleteImage($product->img_url);
        }

        return $product->forceDelete();
    }
}
