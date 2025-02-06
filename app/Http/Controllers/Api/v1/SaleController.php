<?php

namespace App\Http\Controllers\API\v1;

use App\Services\TelegramBotSV;  // Import the TelegramBotSV service
use Illuminate\Http\Request;
use App\Http\Controllers\Api\v1\BaseAPI;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleController extends BaseAPI
{
    protected $telegramBot;

    // Inject the TelegramBotSV service
    public function __construct(TelegramBotSV $telegramBot)
    {
        $this->telegramBot = $telegramBot;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Insert sale and get sale ID
            $saleId = DB::table('sales')->insertGetId([
                'sale_type' => $request->sale_type,
                'total_price' => $request->total_price,
                'payment_method' => $request->payment_method,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Insert sale items with product_id and update stock
            foreach ($request->sale_items as $item) {
                // Retrieve the product_id using subproduct_id
                $productId = DB::table('subproducts')
                    ->where('id', $item['subproduct_id'])
                    ->value('product_id');

                if (!$productId) {
                    throw new \Exception("Product not found for subproduct_id: " . $item['subproduct_id']);
                }

                // Calculate total_price for the item
                $totalPrice = $item['quantity'] * $item['price'];

                // Insert into sale_items with product_id and total_price
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

                DB::table('subproducts')
                ->where('id', $item['subproduct_id'])
                ->update([
                    'stockOut' => DB::raw('stockOut + ' . (int)$item['quantity'])
                ]);

                // Decrease the current stock by the quantity sold
                DB::table('subproducts')
                    ->where('id', $item['subproduct_id'])
                    ->update(['currentStock' => DB::raw('currentStock - ' . (int)$item['quantity'])]); // Decrease currentStock by quantity sold

            }

            // Generate and insert invoice
            $invoiceId = DB::table('invoices')->insertGetId([
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'sale_id' => $saleId,
                'total_amount' => $request->total_amount,
                'discount' => $request->discount ?? 0,
                'service_charge' => $request->service_charge ?? 0,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'total_price' => $request->total_amount,
                'notes' => $request->notes,
                'invoice_date' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Insert payment
            DB::table('payments')->insert([
                'invoice_id' => $invoiceId,
                'amount_paid' => $request->payment_amount,
                'payment_method' => $request->payment_method,
                'payment_date' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();



            return response()->json([
                'sale_id' => $saleId,
                'invoice_id' => $invoiceId,
                'message' => 'Sale, Invoice, and Payment successfully created.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Transaction failed', 'message' => $e->getMessage()], 500);
        }
    }
}
