<?php

namespace App\Http\Controllers\Api\v1;

use App\Services\StockSv;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\v1\BaseAPI;
use App\Models\Stock;

class StockController extends BaseAPI
{
    protected $stockService;
    public function __construct()
    {
        $this->stockService = new StockSv();
    }

    public function index(Request $request)
    {
        try {
            $params = $request->all();
            $stocks = $this->stockService->getAllStocks($params);
            return $this->successResponse($stocks, 'Stocks retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
    public function addStock(Request $request)
    {
        try {
            $params = $request->all();
            $stock = $this->stockService->incrementStock($params);
            return $this->successResponse($stock, 'Stock created successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    //stocker the stock adjustment - stock will be updated with stocker information
    public function subtractStock( Request $request)
    {
        try {

            $params = $request->all();
            // Decrement the stock using the service
            $updatedStock = $this->stockService->decrementStock( $params);

            return $this->successResponse($updatedStock, 'Stock updated successfully.' );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }




    public function update($id, Request $request)
    {
        try {
            $params = $request->all();
            $stock = $this->stockService->updateStock($id, $params);
            return $this->successResponse($stock, 'Stock updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
    public function destroy($id)
    {
        try {
            $stock = $this->stockService->deleteStock($id);
            return $this->successResponse($stock, 'Stock deleted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
    public function updateStock($id, Request $request)
    {
        try {
            $params = $request->all();
            $stock = $this->stockService->updateStock($id, $params);
            return $this->successResponse($stock, 'Stock updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
}
