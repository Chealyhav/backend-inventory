<?php

namespace App\Services;

use App\Models\Stock;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Services\TelegramBotSV;  // Import the TelegramBotSV service

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
            // Calculate stock
            $params['stock'] = $params['stock_in'] - $params['stock_out'];


            $subproduct = DB::table('subproducts')->where('id', $params['subproduct_id'])->first();
            // Create a new stock entry
            $stock = $this->getQuery()->create($params);
            DB::table('subproducts')
                ->where('id', $params['subproduct_id'])
                ->update([
                    'currentStock' => DB::raw('"currentStock" + ' . (int) $params['stock_in']),
                    'total_weight' => DB::raw('"total_weight" + ' . ((int) $params['stock_in'] * (float) $subproduct->unit_weight)),
                    'stockIn' => DB::raw('"stockIn" + ' . (int) $params['stock_in'])
                ]);

            $telegramService = new TelegramBotSV();
            $messageParams = [
                'action' => 'add_stock',
                'product_name' => 'Subproduct ' . ' (' . $subproduct->code . ')',
                'quantity' => $params['stock_in'],
            ];

            // Send the message using the TelegramBotSV service
            $telegramService->sendTrackerProduct($messageParams);

            return $stock;
        } catch (Exception $e) {
            throw new Exception('Error creating stock entry: ' . $e->getMessage());
        }
    }
    //sell the stock - stock will be updated with stocker information
    public function decrementStock(array $params)
    {
        try {
            // Step 1: Validate required parameters
            if (!isset($params['subproduct_id'], $params['stock_out'])) {
                throw new Exception('Missing required fields: subproduct_id or stock_out.');
            }

            // Ensure stock_out is a positive number
            if ($params['stock_out'] <= 0) {
                throw new Exception('Stock out quantity must be greater than zero.');
            }

            // Step 2: Fetch the current stock for the given subproduct_id
            $subproduct = DB::table('subproducts')->where('id', $params['subproduct_id'])->first();

            if (!$subproduct) {
                throw new ModelNotFoundException('Subproduct not found.');
            }

            // Step 3: Validate sufficient stock availability
            if ($subproduct->currentStock < $params['stock_out']) {
                throw new Exception('Insufficient stock available for the requested operation.');
            }

            // Step 4: Calculate remaining stock after decrement
            $newStock = $subproduct->currentStock - $params['stock_out'];

            // Default for stock_out if not set, and default stock_date if not set
            $params['stock_in'] = $params['stock_in'] ?? 0;
            // Step 5: Create a new stock entry for the given subproduct
            $stockEntry = $this->getQuery()->create([
                'subproduct_id' => $params['subproduct_id'],
                'description' => $params['description'] ?? 'Stock adjustment',
                'stock_out' => $params['stock_out'],
                'currentStock' => $newStock,
                'stock_in' => $params['stock_in'],
            ]);

            if (!$stockEntry) {
                throw new Exception('Failed to create new stock entry.');
            }

            // Step 6: Update the subproduct's stock in the subproducts table
            DB::table('subproducts')
                ->where('id', $params['subproduct_id'])
                ->update([
                    'currentStock' => DB::raw('"subproducts"."currentStock" - ' . (int)$params['stock_out']),
                    'stockOut' => DB::raw('"subproducts"."stockOut" + ' . (int)$params['stock_out']),
                ]);

            // Step 7: Send a Telegram notification (optional)
            $telegramService = new TelegramBotSV();

            // Check if stock is low and send a notification
            if ($newStock <= 4) {
                $messageParams = [
                    'action' => 'checkStock',
                    'product_name' => 'Subproduct (' . $subproduct->code . ')',
                    'quantity' => $params['stock_out'],
                ];
                $telegramService->sendTrackerProduct($messageParams);
            }
            // Step 8: notification for stock added
            // Check if stock is added and send a notification
            $messageParams = [
                'action' => 'adjust_stock',
                'product_name' => 'Subproduct ' . ' (' . $subproduct->code . ')',
                'quantity' => $params['stock_in'],
            ];

            // Send the message using the TelegramBotSV service
            $telegramService->sendTrackerProduct($messageParams);

            return $stockEntry;
        } catch (Exception $e) {
            throw new Exception('Error decrementing stock: ' . $e->getMessage());
        }
    }


    /**
     * Get all stock records
     *
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Collection
     */ public function getAllStocks($params = [])
    {
        $query = $this->getQuery()
            ->leftJoin('subproducts', 'stocks.subproduct_id', '=', 'subproducts.id')
            ->leftJoin('products', 'subproducts.product_id', '=', 'products.id')
            ->select(
                'stocks.id',
                'stocks.stock_in',
                'stocks.stock_out',
                'stocks.stock',
                'stocks.created_at',
                'stocks.updated_at',
                'subproducts.code as subproduct_code',
                'products.product_name',
                'stocks.description'

            )
            ->orderBy('stocks.created_at', 'asc');

        // Apply filters if any
        if (isset($params['product_id'])) {
            $query->where('products.id', $params['product_id']);
        }
        if (isset($params['subproduct_id'])) {
            $query->where('subproducts.id', $params['subproduct_id']);
        }

        // Search filter
        if (isset($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('subproducts.code', 'LIKE', '%' . $params['search'] . '%')
                    ->orWhere('products.product_name', 'LIKE', '%' . $params['search'] . '%');
            });
        }

        // Filter by stock type
        if (isset($params['stock_type'])) {
            if ($params['stock_type'] === 'in') {
                $query->where('stocks.stock_in', '>', 0);
            } elseif ($params['stock_type'] === 'out') {
                $query->where('stocks.stock_out', '>', 0);
            }
        }

        // Pagination
        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        $query->skip($offset)->take($limit);

        $results = $query->get();
        $total = $query->count();

        return [
            'total' => $total,
            'totalPage' => ceil($total / $limit),
            'nextPage' => $page + 1,
            'prevPage' => $page > 1 ? $page - 1 : null,
            'currentPage' => $page,
            'limit' => $limit,
            'data' => $results,
        ];
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
