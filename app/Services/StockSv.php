<?php

namespace App\Services;

use App\Models\Stock;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

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
            // Ensure required fields are provided
            if (!isset($params['subproduct_id']) || !isset($params['stock_in'])) {
                throw new Exception('Missing required fields for stock entry.');
            }

            // Default for stock_out if not set, and default stock_date if not set
            $params['stock_out'] = $params['stock_out'] ?? 0;
            $params['stock_date'] = $params['stock_date'] ?? now()->toDateString();

            // Calculate stock
            $params['stock'] = $params['stock_in'] - $params['stock_out'];

            // Create a new stock entry
            $stock = $this->getQuery()->create($params);

            // Update the subproduct's current stock and stockin using a raw SQL query and count auto
            DB::table('subproducts')
                ->where('id', $params['subproduct_id'])
                ->update(['currentStock' => DB::raw('currentStock + ' . $params['stock_in']), 'stockin' => DB::raw('stockin + ' . $params['stock_in'])]);
            return $stock;
        } catch (Exception $e) {
            throw new Exception('Error creating stock entry: ' . $e->getMessage());
        }
    }

    //sell the stock - stock will be updated
    public function decrementStock(array $params)
    {
        try {
            // Ensure required parameters are provided
            if (!isset($params['subproduct_id']) || !isset($params['stock_out'])) {
                throw new Exception('Missing required fields for stock entry.');
            }

            // Fetch the current stock for the given subproduct_id
            $currentStock = $this->getQuery()->where('subproduct_id', $params['subproduct_id'])->first();

            if (!$currentStock) {
                throw new ModelNotFoundException('Stock not found.');
            }

            // Calculate remaining stock after decrement
            $newStock = $currentStock->currentStock - $params['stock_out'];

            // Update the stock entry for the given subproduct
            $currentStock->update([
                'stock_out' => $currentStock->stock_out + $params['stock_out'],
                'currentStock' => $newStock,
            ]);

            // Update the subproduct's stock
            DB::table('subproducts')
                ->where('id', $params['subproduct_id'])
                ->update([
                    'currentStock' => DB::raw('currentStock - ' . $params['stock_out']),
                    'stockOut' => DB::raw('stockOut + ' . $params['stock_out']),
                ]);
            return $currentStock;
        } catch (Exception $e) {
            throw new Exception('Error decrementing stock: ' . $e->getMessage());
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
