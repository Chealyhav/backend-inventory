<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PosSV extends BaseService
{
    /**
     * Create a new sale transaction.
     */
    public function createSaleTransaction(array $params)
    {
        DB::beginTransaction();

        try {
            $saleId = $this->createSale($params);
            $this->addSaleItems($params, $saleId);
            $invoiceId = $this->processPayment($params, $saleId);

            DB::commit();

            Log::info("Sale transaction completed successfully.", ['sale_id' => $saleId, 'invoice_id' => $invoiceId]);
            return $saleId;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Sale transaction failed.", ['error' => $e->getMessage()]);
            throw new \Exception('Sale creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Create a new sale.
     */
    public function createSale(array $params)
    {
        $saleId = DB::table('sales')->insertGetId([
            'sale_type' => $params['sale_type'],
            'title' => $params['title'] ?? null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($params['sale_type'] === 'finished_good') {
            $this->addSaleItems($params, $saleId);
        }

        Log::info("Sale created.", ['sale_id' => $saleId]);
        return $saleId;
    }

    /**
     * Add sale items to a sale.
     */
    public function addSaleItems(array $params, $saleId)
    {
        $saleItems = $params['sale_items'];

        foreach ($saleItems as $item) {
            $productId = DB::table('subproducts')
                ->where('id', $item['subproduct_id'])
                ->value('product_id');

            if (!$productId) {
                throw new \Exception("Product not found for subproduct_id: " . $item['subproduct_id']);
            }

            $totalPrice = $item['quantity'] * $item['price'];

            DB::table('sale_items')->insert([
                'sale_id' => $saleId,
                'product_id' => $productId,
                'subproduct_id' => $item['subproduct_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total_price' => $totalPrice,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $currentStock = DB::table('subproducts')
                ->where('id', $item['subproduct_id'])
                ->value('currentStock');

            if ($currentStock < $item['quantity']) {
                throw new \Exception("Not enough stock for subproduct_id: " . $item['subproduct_id']);
            }

            DB::table('subproducts')
                ->where('id', $item['subproduct_id'])
                ->update([
                    'stockOut' => DB::raw('stockOut + ' . (int)$item['quantity']),
                    'currentStock' => DB::raw('currentStock - ' . (int)$item['quantity'])
                ]);

            Log::info("Sale item added.", ['sale_id' => $saleId, 'subproduct_id' => $item['subproduct_id']]);
        }

        return $saleId;
    }

    /**
     * Process payment for a sale.
     */
    public function processPayment(array $params, $saleId)
    {
        $invoiceId = DB::table('invoices')->insertGetId([
            'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
            'sale_id' => $saleId,
            'total_amount' => $params['total_amount'],
            'discount' => $params['discount'] ?? 0,
            'service_charge' => $params['service_charge'] ?? 0,
            'payment_method' => $params['payment_method'],
            'payment_status' => 'pending',
            'total_price' => $params['total_amount'],
            'notes' => $params['notes'] ?? null,
            'invoice_date' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('payments')->insert([
            'invoice_id' => $invoiceId,
            'amount_paid' => $params['payment_amount'],
            'payment_method' => $params['payment_method'],
            'payment_date' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Log::info("Payment processed.", ['invoice_id' => $invoiceId, 'sale_id' => $saleId]);
        return $invoiceId;
    }


     public function orderDetails($id)
     {
        $data = DB::table('sales')
        ->where('id', $id)
        ->with('saleItems')
        ->first();
        return $data;

     }


}
