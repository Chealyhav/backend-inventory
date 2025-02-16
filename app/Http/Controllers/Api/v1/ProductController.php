<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Requests\ProductsRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\v1\BaseAPI;
use App\Services\ProductSV;

class ProductController extends BaseAPI
{
    protected $productSV;

    public function __construct(ProductSV $productSV)
    {
        $this->productSV = $productSV;
    }

    /**
     * Get all products.
     */
    public function index(Request $request)
    {
        try {
            $params = $request->all();
            $products = $this->productSV->getAllProducts($params);
            return $this->sendResponse($products, 'Products retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(),$e->getCode());
        }
    }

    /**
     * Get a single product by ID.
     */
    public function show($id)
    {
        try {
            $product = $this->productSV->getProductById($id);
            return $this->sendResponse($product, 'Product retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(),$e->getCode());
        }
    }

    /**
     * Create a new product.
     */
    public function store(request $request)
    {
        try {
            $params = $request->all();
            $product = $this->productSV->createProduct($params);
            return $this->sendResponse($product, 'Product created successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(),$e->getCode());
        }
    }

    /**
     * Update an existing product.
     */
    public function update(Request $request, $id)
    {
        try {
            $params = $request->all();
            $product = $this->productSV->updateProduct($id, $params);
            return $this->sendResponse($product, 'Product updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Soft delete a product.
     */
    public function destroy($id)
    {
        try {
            $this->productSV->deleteProduct($id);
            return $this->sendResponse([], 'Product deleted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode ());
        }
    }

    /**
     * Restore a soft-deleted product.
     */
    public function restore($id)
    {
        try {
            $this->productSV->restoreProduct($id);
            return $this->sendResponse([], 'Product restored successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Permanently delete a product.
     */
    public function forceDelete($id)
    {
        try {
            $this->productSV->deleteProductFromDb($id);
            return $this->sendResponse([], 'Product permanently deleted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }


}
