<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\SubProductsRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\v1\BaseAPI;
use App\Services\SubProductSV;

class SubProductController extends BaseAPI
{
    protected $subProductService;


    public function __construct()
    {
    $this->subProductService = new SubProductSV();
    }


    // Get all subproducts with filters and pagination
    public function index(Request $request)
    {
        try {
            $params = $request->all();
            $subProducts = $this->subProductService->getAllSubProducts($params);
            return $this->successResponse($subProducts, 'SubProducts retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    // Get a single subproduct by ID
    public function show($id)
    {
        try {
            $subProduct = $this->subProductService->getSubProductById($id);
            return $this->successResponse($subProduct, 'SubProduct retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    // Create a new subproduct
    public function store(request $request)
    {
        try {
            $params = $request->all();
            $subProduct = $this->subProductService->createSubProduct( $params);
            return $this->successResponse($subProduct, 'SubProduct created successfully.', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    // Update an existing subproduct
    public function update($id, SubProductsRequest $request)
    {
        try {
            $params = $request->all();
            $subProduct = $this->subProductService->updateSubProduct( $id, $params );
            return $this->successResponse($subProduct, 'SubProduct updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    // Soft delete a subproduct
    public function destroy($id)
    {
        try {

            $subProduct = $this->subProductService->SubProductDelete($id);
            return $this->successResponse($subProduct, 'SubProduct deleted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    // Restore a soft-deleted subproduct
    public function restore($id)
    {
        try {
            $subProduct = $this->subProductService->restoreSubProduct($id);
            return $this->successResponse($subProduct, 'SubProduct restored successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    // Permanently delete a subproduct
    public function forceDelete($id)
    {
        try {
            $subProduct = $this->subProductService->SubProductDelete($id);
            return $this->successResponse($subProduct, 'SubProduct permanently deleted.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
}
