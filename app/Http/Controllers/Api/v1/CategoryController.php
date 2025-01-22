<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\BaseAPI;
use App\Services\CategorySV;
use Illuminate\Http\Request;

class CategoryController extends BaseAPI
{
    protected $categoryService;

    public function __construct(CategorySV $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Get all categories
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $params = $request->all();
            $categories = $this->categoryService->getAllCategories($params);
            return $this->sendResponse($categories, 'Categories retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get a category by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $category = $this->categoryService->getCategoryById($id);
            return $this->sendResponse($category, 'Category retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    /**
     * Create a new category
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $params = $request->all();
            $category = $this->categoryService->createCategory($params);
            return $this->sendResponse($category, 'Category created successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update an existing category
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        try {
            $data = $request->only(['name', 'status', 'description']);
            $category = $this->categoryService->updateCategory($id, $data);
            return $this->sendResponse($category, 'Category updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    /**
     * Soft delete a category
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $this->categoryService->deleteCategory($id);
            return $this->sendResponse(null, 'Category deleted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    /**
     * Restore a soft-deleted category
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        try {
            $this->categoryService->restoreCategory($id);
            return $this->sendResponse(null, 'Category restored successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    /**
     * Permanently delete a category
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete($id)
    {
        try {
            $this->categoryService->deleteCategoryFromDb($id);
            return $this->sendResponse(null, 'Category permanently deleted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }
}
