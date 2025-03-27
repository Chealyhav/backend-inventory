<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\v1\BaseAPI;
use App\Services\SubcategorySV;
use Swagger\Annotations as SWG;

class SubCategoryController extends BaseAPI
{
    protected $subCategorySV;

    public function __construct()
    {
        $this->subCategorySV = new SubcategorySV();
    }


    public function index(Request $request)
    {
        try {
            $params = $request->all();
            $subcategories = $this->subCategorySV->getAllSubcategories($params);
            return $this->sendResponse($subcategories, 'Subcategories retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    public function show($id)
    {
        try {
            $subcategory = $this->subCategorySV->getSubcategoryById($id);
            return $this->sendResponse($subcategory, 'Subcategory retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

       public function store(Request $request)
    {
        try {
            $params = $request->all();
            $subcategory = $this->subCategorySV->createSubcategory($params);
            return $this->sendResponse($subcategory, 'Subcategory created successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $data = $request->only(['name', 'status', 'category_id']);
            $subcategory = $this->subCategorySV->updateSubcategory($id, $data);
            return $this->sendResponse($subcategory, 'Subcategory updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

       public function destroy($id)
    {
        try {
            $deleted = $this->subCategorySV->deleteSubcategory($id);
            return $this->sendResponse($deleted, 'Subcategory archived successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    public function restore($id)
    {
        try {
            $restored = $this->subCategorySV->restoreSubcategory($id);
            return $this->sendResponse($restored, 'Subcategory restored successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
