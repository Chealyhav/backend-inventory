<?php

namespace App\Http\Controllers\Api\v1;

use App\Services\SaleSV;
use App\Services\TelegramBotSV;  // Import the TelegramBotSV service
use Illuminate\Http\Request;
use App\Http\Controllers\Api\v1\BaseAPI;
use App\Services\PosSV;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class SaleController extends BaseAPI
{
    protected $telegramBot;
    protected $posService;
    protected $saleService;

    // Inject the TelegramBotSV and PosSV services
    public function __construct(TelegramBotSV $telegramBot, PosSV $posService , SaleSV $saleService)
    {
        $this->telegramBot = $telegramBot;
        $this->posService = $posService;
        $this->saleService = $saleService;

    }

       /**
     * sale transaction
     *
     */
    public function saleTransaction(Request $request)
    {
        try {
            $params = $request->all();
            $saleId = $this->posService->createSale($params);
            return $this->sendResponse($saleId, 'Sale created successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Create a new sale.
     */
    public function store(Request $request)
    {
        try {
            $params = $request->all();
            $saleId = $this->posService->createSale($params);

            return $this->sendResponse($saleId, 'Sale created successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store sale items -order  and notify via Telegram.
     */
    public function storeItems(Request $request)
    {
        try {
            $params = $request->all();
            $saleId = $params['sale_id'];

            $saleItems = $this->posService->addSaleItems($params, $saleId);

            // Send notification to Telegram for each item
            foreach ($params['items'] as $item) {
                $this->telegramBot->sendTrackerProduct([
                    'action' => 'sell',
                    'product_name' => $item['product_name'] ?? 'Unknown Product',
                    'quantity' => $item['quantity'],
                ]);
            }

            return $this->sendResponse($saleItems, 'Sale items created and notifications sent successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Process payment for a sale.
     */
    public function processPayment(Request $request)
    {
        try {
            $params = $request->all();
            $saleId = $params['sale_id'];

            $invoiceId = $this->posService->processPayment($params, $saleId);

            // Notify about payment via Telegram
            $this->telegramBot->sendTrackerProduct([
                'action' => 'payment',
                'product_name' => 'Payment Processed',
                'quantity' => $params['payment_amount'] ?? 0,
            ]);

            return $this->sendResponse($invoiceId, 'Payment processed successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    // public function store(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         // Insert sale and get sale ID
    //         $saleId = DB::table('sales')->insertGetId([
    //             'sale_type' => $request->sale_type,
    //             'total_price' => $request->total_price,
    //             'payment_method' => $request->payment_method,
    //             'created_at' => now(),
    //             'updated_at' => now()
    //         ]);

    //         // Insert sale items with product_id and update stock
    //         foreach ($request->sale_items as $item) {
    //             // Retrieve the product_id using subproduct_id
    //             $productId = DB::table('subproducts')
    //                 ->where('id', $item['subproduct_id'])
    //                 ->value('product_id');

    //             if (!$productId) {
    //                 throw new \Exception("Product not found for subproduct_id: " . $item['subproduct_id']);
    //             }

    //             // Calculate total_price for the item
    //             $totalPrice = $item['quantity'] * $item['price'];

    //             // Insert into sale_items with product_id and total_price
    //             DB::table('sale_items')->insert([
    //                 'sale_id' => $saleId,
    //                 'product_id' => $productId,
    //                 'subproduct_id' => $item['subproduct_id'],
    //                 'quantity' => $item['quantity'],
    //                 'price' => $item['price'],
    //                 'total_price' => $totalPrice,
    //                 'created_at' => now(),
    //                 'updated_at' => now()
    //             ]);

    //             DB::table('subproducts')
    //             ->where('id', $item['subproduct_id'])
    //             ->update([
    //                 'stockOut' => DB::raw('stockOut + ' . (int)$item['quantity'])
    //             ]);

    //             // Decrease the current stock by the quantity sold
    //             DB::table('subproducts')
    //                 ->where('id', $item['subproduct_id'])
    //                 ->update(['currentStock' => DB::raw('currentStock - ' . (int)$item['quantity'])]); // Decrease currentStock by quantity sold

    //         }

    //         // Generate and insert invoice
    //         $invoiceId = DB::table('invoices')->insertGetId([
    //             'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
    //             'sale_id' => $saleId,
    //             'total_amount' => $request->total_amount,
    //             'discount' => $request->discount ?? 0,
    //             'service_charge' => $request->service_charge ?? 0,
    //             'payment_method' => $request->payment_method,
    //             'payment_status' => 'pending',
    //             'total_price' => $request->total_amount,
    //             'notes' => $request->notes,
    //             'invoice_date' => now(),
    //             'created_at' => now(),
    //             'updated_at' => now()
    //         ]);

    //         // Insert payment
    //         DB::table('payments')->insert([
    //             'invoice_id' => $invoiceId,
    //             'amount_paid' => $request->payment_amount,
    //             'payment_method' => $request->payment_method,
    //             'payment_date' => now(),
    //             'created_at' => now(),
    //             'updated_at' => now()
    //         ]);

    //         DB::commit();



    //         return response()->json([
    //             'sale_id' => $saleId,
    //             'invoice_id' => $invoiceId,
    //             'message' => 'Sale, Invoice, and Payment successfully created.'
    //         ], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['error' => 'Transaction failed', 'message' => $e->getMessage()], 500);
    //     }
    // }





    public function createOrder(Request $request)
    {
        try {
            $params = $request->all();
            $orderId = $this->saleService->createOrder($params);
            return $this->successResponse($orderId, 'Order created successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    //payment
    public function createPayment(Request $request)
    {
        try {
            $params = $request->all();
            $paymentId = $this->saleService->createPayment($params);
            return $this->successResponse($paymentId, 'Payment created successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
    // public function createInvoice(Request $request)
    // {
    //     try {
    //         $params = $request->all();
    //         $invoiceId = $this->saleService->createInvoice($params);
    //         return $this->sendResponse($invoiceId, 'Invoice created successfully.');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse($e->getMessage(), $e->getCode());
    //     }
    // }
    // public function createPayment(Request $request)
    // {
    //     try {
    //         $params = $request->all();
    //         $paymentId = $this->saleService->createPayment($params);
    //         return $this->sendResponse($paymentId, 'Payment created successfully.');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse($e->getMessage(), $e->getCode());
    //     }
    // }
    // public function createSale(Request $request)
    // {
    //     try {
    //         $params = $request->all();
    //         $saleId = $this->saleService->createSale($params);
    //         return $this->sendResponse($saleId, 'Sale created successfully.');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse($e->getMessage(), $e->getCode());
    //     }
    // }
    // public function getOrderDetails($orderId)
    // {
    //     try {
    //         $orderDetails = $this->saleService->getOrderDetails($orderId);
    //         return $this->sendResponse($orderDetails, 'Order details retrieved successfully.');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse($e->getMessage(), $e->getCode());
    //     }
    // }
    // public function indexPayment(Request $request)
    // {
    //     try {
    //         $params = $request->all();
    //         $payments = $this->posService->getPayments($params);
    //         return $this->sendResponse($payments, 'Payments retrieved successfully.');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse($e->getMessage(), $e->getCode());
    //     }
    // }
    // public function showPayment($id)
    // {
    //     try {
    //         $payment = $this->posService->getPaymentById($id);
    //         return $this->sendResponse($payment, 'Payment retrieved successfully.');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse($e->getMessage(), $e->getCode());
    //     }
    // }
    // public function updatePayment(Request $request, $id)
    // {
    //     try {
    //         $params = $request->all();
    //         $updatedPayment = $this->posService->updatePayment($id, $params);
    //         return $this->sendResponse($updatedPayment, 'Payment updated successfully.');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse($e->getMessage(), $e->getCode());
    //     }
    // }

}
