<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\v1\BaseAPI;
use App\Services\CategorySV;

class CategoryController extends BaseAPI
{
    protected $category;
    public function __construct()
    {
        $this->category = new CategorySV();
    }
    public function index(Request $request)
    {
        try {
            $params = $request->all();
            $categories = $this->category->CategoryList($params);
            return $this->sendResponse($categories, 'Categories retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // Create a new category
    public function store(request $request)
    {
        try {
            $params = $request->all();
            $category = $this->category->CategoryCreate($params);
            return $this->sendResponse($category, 'Category created successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // Update a category
    public function update($id, request $request)
    {
        try {
            $params = $request->all();
            $category = $this->category->CategoryUpdate($id, $params);
            return $this->sendResponse($category, 'Category updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // Delete a category
    public function destroy($id)
    {
        try {
            $category = $this->category->CategoryDelete($id);
            return $this->sendResponse($category, 'Category deleted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
