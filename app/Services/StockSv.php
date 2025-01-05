<?php

namespace App\Services;

use App\Models\Stock;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StockSv extends BaseService
{
    public function getQuery()
    {
        return Stock::query();
    }

    /**
     * Create a stock entry
     *
     * @param array $params
     * @return Stock
     * @throws Exception
     */
    public function incrementStock(array $params)
    {
        try {
            // Ensure required parameters are present
            if (!isset($params['subproduct_id']) || !isset($params['stock_in'])) {
                throw new Exception('Missing required fields for stock entry.');
            }

            // Set stock_out to 0 if it's not provided in the request
            $params['stock_out'] = $params['stock_out'] ?? 0;  // Use the null coalescing operator to set a default value if not provided

            // Set stock_date to today's date if not provided
            if (!isset($params['stock_date'])) {
                $params['stock_date'] = now()->toDateString(); // Or use Carbon::now()->toDateString()
            }

            // Calculate stock value
            $params['stock'] = $params['stock_in'] - $params['stock_out'];

            // Create new stock record
            $stock = $this->getQuery()->create($params);
            return $stock;
        } catch (Exception $e) {
            throw new Exception('Error creating stock entry: ' . $e->getMessage());
        }
    }


    //sell the stock - stock will be updated
    public function decrementStock(Stock $stock, array $params)
    {
        try {
            // Ensure required parameters are present
            if (!isset($params['stock_out'])) {
                throw new Exception('Missing required fields for stock entry.');
            }
            // Calculate stock value
            $params['stock'] = $params['stock_in'] - $params['stock_out'];
            $stock->update($params);
            return $stock;
        } catch (Exception $e) {
            throw new Exception('Error creating stock entry: ' . $e->getMessage());
        }
    }

    /**
     * Get all stock records
     *
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllStocks($params = [])
    {
        $query = $this->getQuery();

        // Apply filters if any
        if (isset($params['subproduct_id'])) {
            $query->where('subproduct_id', $params['subproduct_id']);
        }

        // Pagination
        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        $query->skip($offset)->take($limit);

        // Fetch results
        return $query->get();
    }

    /**
     * Get a stock entry by ID
     *
     * @param int $id
     * @return Stock
     * @throws ModelNotFoundException
     */
    public function getStockById($id)
    {
        $stock = $this->getQuery()->find($id);

        if (!$stock) {
            throw new ModelNotFoundException('Stock not found.');
        }

        return $stock;
    }

    /**
     * Update a stock entry
     *
     * @param int $id
     * @param array $params
     * @return Stock
     * @throws ModelNotFoundException
     */
    public function updateStock($id, array $params)
    {
        $stock = $this->getQuery()->find($id);

        if (!$stock) {
            throw new ModelNotFoundException('Stock not found.');
        }

        // Calculate new stock if stock_in or stock_out is changed
        if (isset($params['stock_in']) || isset($params['stock_out'])) {
            $params['stock'] = ($params['stock_in'] ?? $stock->stock_in) - ($params['stock_out'] ?? $stock->stock_out);
        }

        $stock->update($params);

        return $stock;
    }

    /**
     * Soft delete a stock entry
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deleteStock($id)
    {
        $stock = $this->getQuery()->find($id);

        if (!$stock) {
            throw new ModelNotFoundException('Stock not found.');
        }

        return $stock->delete();
    }

    /**
     * Restore a soft-deleted stock entry
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function restoreStock($id)
    {
        $stock = $this->getQuery()->withTrashed()->find($id);

        if (!$stock) {
            throw new ModelNotFoundException('Stock not found or not archived.');
        }

        return $stock->restore();
    }

    /**
     * Permanently delete a stock entry from the database
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deleteStockFromDb($id)
    {
        $stock = $this->getQuery()->withTrashed()->find($id);

        if (!$stock) {
            throw new ModelNotFoundException('Stock not found.');
        }

        return $stock->forceDelete();
    }
}
