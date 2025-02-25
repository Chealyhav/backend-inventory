<?php

namespace App\Services;

use App\Models\Category;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategorySV extends BaseService
{
    public function getQuery()
    {
        return Category::query();
    }

    /**
     * Get all categories
     *
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCategories($params = [])
    {
        $query = $this->getQuery();
        $data = $query->get();

        if ($data->isEmpty()) {
            throw new Exception('No categories found.');
        }

        return $data;
    }

    /**
     * Get a category by ID
     *
     * @param int $id
     * @return Category
     * @throws ModelNotFoundException
     */
    public function getCategoryById($id)
    {
        $category = $this->getQuery()->find($id);

        if (!$category) {
            throw new ModelNotFoundException('Category not found.');
        }

        return $category;
    }

    /**
     * Create a category
     *
     * @param array $data
     * @return Category
     */
    public function createCategory(array $data)
    {
        return $this->getQuery()->create($data);
    }

    /**
     * Update a category
     *
     * @param int $id
     * @param array $data
     * @return Category
     * @throws ModelNotFoundException
     */
    public function updateCategory($id, array $data)
    {
        $category = $this->getQuery()->find($id);

        if (!$category) {
            throw new ModelNotFoundException('Category not found.');
        }

        $category->update($data);

        return $category;
    }

    /**
     * Soft delete a category
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deleteCategory($id)
    {
        $category = $this->getQuery()->find($id);

        if (!$category) {
            throw new ModelNotFoundException('Category not found.');
        }

        return $category->delete();
    }

    /**
     * Restore a soft-deleted category
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function restoreCategory($id)
    {
        $category = $this->getQuery()->withTrashed()->find($id);

        if (!$category) {
            throw new ModelNotFoundException('Category not found or not archived.');
        }

        return $category->restore();
    }

    /**
     * Permanently delete a category
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deleteCategoryFromDb($id)
    {
        $category = $this->getQuery()->withTrashed()->find($id);

        if (!$category) {
            throw new ModelNotFoundException('Category not found.');
        }

        return $category->forceDelete();
    }
}
