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

    /**
     * @SWG\Get(
     *     path="/api/subcategories",
     *     summary="Get all subcategories",
     *     tags={"Subcategory"},
     *     @SWG\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter by subcategory name",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Subcategories retrieved successfully",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Subcategory")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
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

    /**
     * @SWG\Get(
     *     path="/api/subcategories/{id}",
     *     summary="Get a single subcategory by ID",
     *     tags={"Subcategory"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of subcategory to return",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Subcategory retrieved successfully",
     *         @SWG\Schema(ref="#/definitions/Subcategory")
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Subcategory not found"
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $subcategory = $this->subCategorySV->getSubcategoryById($id);
            return $this->sendResponse($subcategory, 'Subcategory retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @SWG\Post(
     *     path="/api/subcategories",
     *     summary="Create a new subcategory",
     *     tags={"Subcategory"},
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Name of the subcategory",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="status",
     *         in="formData",
     *         description="Status of the subcategory",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="category_id",
     *         in="formData",
     *         description="Category ID that the subcategory belongs to",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Subcategory created successfully",
     *         @SWG\Schema(ref="#/definitions/Subcategory")
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
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

    /**
     * @SWG\Put(
     *     path="/api/subcategories/{id}",
     *     summary="Update an existing subcategory",
     *     tags={"Subcategory"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the subcategory to update",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Updated name of the subcategory",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="status",
     *         in="formData",
     *         description="Updated status of the subcategory",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="category_id",
     *         in="formData",
     *         description="Updated category ID",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Subcategory updated successfully",
     *         @SWG\Schema(ref="#/definitions/Subcategory")
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
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

    /**
     * @SWG\Delete(
     *     path="/api/subcategories/{id}",
     *     summary="Soft delete a subcategory",
     *     tags={"Subcategory"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the subcategory to delete",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Subcategory archived successfully"
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->subCategorySV->deleteSubcategory($id);
            return $this->sendResponse($deleted, 'Subcategory archived successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @SWG\Post(
     *     path="/api/subcategories/{id}/restore",
     *     summary="Restore an archived subcategory",
     *     tags={"Subcategory"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the subcategory to restore",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Subcategory restored successfully"
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
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
