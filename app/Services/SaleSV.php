<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleSV extends BaseService
{

    /**
     * Create a new sale transaction.
     */
    public function getQuery()
    {
        return Order::query();
    }
    /**
     * Create a new order with items and update stock accordingly and return the created order and have status paid.
     *
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function createOrder(array $params = [])
    {
        try {
            DB::beginTransaction();

            // Validate required parameters
            if (empty($params['items']) || !is_array($params['items'])) {
                throw new \InvalidArgumentException('Order items are required.');
            }

            $allowedSaleTypes = ['Finished Good', 'Material'];
            if (!in_array($params['sale_type'] ?? '', $allowedSaleTypes)) {
                throw new \InvalidArgumentException('Invalid sale_type. Allowed values: ' . implode(', ', $allowedSaleTypes));
            }

            //if sale_type  == Finished Good   for order have title
            if ($params['sale_type'] === 'Finished Good' && empty($params['title'])) {
                throw new \InvalidArgumentException('Title is required for Finished Good sale type.');
            }
            // Create the order
            $order = $this->getQuery()->create([
                //order_code make unique and auto-generated
                'order_code'   => $params['order_code'] ?? 'ORD-' . time(), // Generate a unique order code
                'customer_id' => $params['customer_id'] ?? null,
                'sale_type'    => $params['sale_type'] ?? 'POS',
                'total_price'  => $params['total_price'] ?? 0,
                'order_date'   => now(),
                'status'       => $params['status'] ?? false, // Default to false (not paid)
                'title'        => $params['title'] ?? null, // Optional title for Finished Good
                'created_by'   => $params['created_by'] ?? null,
                'created_at'   => now(),
                'updated_at'   => now(),
                'updated_by' => $params['created_by'] ?? null,

            ]);

            $orders = [];

            // Insert order items and update stock
            foreach ($params['items'] as $item) {
                if (
                    !isset($item['subproduct_id'], $item['quantity'], $item['price'])
                    || $item['quantity'] <= 0
                ) {
                    throw new \InvalidArgumentException('Each item must have subproduct_id, quantity (>0), and price.');
                }
                //sub_category_id not found  check
                if (!isset($item['subproduct_id']) || !is_numeric($item['subproduct_id'])) {
                    throw new \InvalidArgumentException('Invalid subproduct_id for item.');
                }

                //select sale_price and stock from subproducts table
                $subproduct = DB::table('subproducts')
                    ->select('sale_price', 'currentStock')
                    ->where('id', $item['subproduct_id'])
                    ->first();
                if (!$subproduct) {
                    throw new \InvalidArgumentException('Subproduct not found for ID: ' . $item['subproduct_id']);
                }
                if ($subproduct->currentStock < $item['quantity']) {
                    throw new \InvalidArgumentException('Not enough stock for subproduct ID: ' . $item['subproduct_id']);
                }

                // Calculate total price for the item
                $item['unit_price'] = (isset($item['price']) && $item['price'] > 0)
                    ? (float) $item['price']
                    : (float) $subproduct->sale_price;
                $item['total_price'] = $item['quantity'] * $item['unit_price'];

                //example: if subproduct_id and product_id when controll stock or sale by
                //stock_type (mm) for  other subproduct have stock_type(any type ex 'kg', 'pcs', etc.) and for sale product  Calculate
                //example: unit_weight = 1.5, quantity = 1, sale_price = 100
                //so subproduct 1 stock = 1.5 * 1 = 1.5 kg , total_price = 1 * 100 = 100$
                // If subproduct has adjust quantity and change value and total_price and unit_type_stock accordingly example: unit_type_stock = 1.5mm, quantity = 1, sale_price = 100$
                // so subproduct 1 stock = 1.5 * 1 = 1.5 mm , total_price = 1 * 100$ = 100$

                //$item['unit_type_stock'] = (isset($item['unit_type_stock']) && $item['unit_type_stock'] > 0)
                //    ? (float) $item['unit_type_stock']
                //    : (float) $subproduct->unit_type_stock;

                // If subproduct has unit_type_stock, calculate total price based on unit_type_stock
                // Create order item
                $orders[] = OrderItem::create([
                    'order_id'      => $order->id,
                    'subproduct_id' => $item['subproduct_id'],
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['unit_price'],
                    'total_price'   => $item['total_price'],
                    'order_date'    => now(),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                // Check if subproduct exists and has enough stock
                $subproduct = DB::table('subproducts')
                    ->where('id', $item['subproduct_id'])
                    ->first();
                if (!$subproduct) {
                    throw new \InvalidArgumentException('Subproduct not found for ID: ' . $item['subproduct_id']);
                }
                if ($subproduct->currentStock < $item['quantity']) {
                    throw new \InvalidArgumentException('Not enough stock for subproduct ID: ' . $item['subproduct_id']);
                }
                // Update stock for the subproduct
                DB::table('subproducts')
                    ->where('id', $item['subproduct_id'])
                    ->update([
                        'currentStock' => DB::raw('"subproducts"."currentStock" - ' . (int)$item['quantity']),
                        'stockOut' => DB::raw('"subproducts"."stockOut" + ' . (int)$item['quantity']),
                    ]);
            }

            DB::commit();
            return $orders;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Order creation error: ' . $e->getMessage());
            throw new \Exception('Order creation failed: ' . $e->getMessage());
        }
    }
    /**
     * Create a payment for an order and update order status if payment is successful.
     *
     * @param array $params
     * @return \App\Models\Payment
     * @throws \Exception
     */

    // createPayment
    public function createPayment(array $params = [])
    {
        try {
            DB::beginTransaction();

            // Validate required parameters
            if (empty($params['order_id']) || !is_numeric($params['order_id'])) {
                throw new \InvalidArgumentException('Order ID is required.');
            }
            // Find the order
            $order = Order::find($params['order_id']);
            if (!$order) {
                throw new \InvalidArgumentException('Order not found for ID: ' . $params['order_id']);
            }

            // Check if the order is already paid and to update status order
            if ($order->status === true) {
                throw new \InvalidArgumentException('Order is already paid for ID: ' . $params['order_id']);
            }


            // Check if the order has items
            $itemCount = DB::table('order_items')->where('order_id', $order->id)->count();
            if ($itemCount === 0) {
                throw new \InvalidArgumentException('Order has no items for ID: ' . $params['order_id']);
            }

            // Check total amount of order items
            $totalAmount = DB::table('order_items')
                ->where('order_id', $order->id)
                ->sum('total_price');

            // If amount is not set or invalid, use totalAmount
            if (empty($params['amount']) || $params['amount'] <= 0) {
                $params['amount'] = $totalAmount;
            }

            // Now, always require amount to match totalAmount
            if ($params['amount'] != $totalAmount) {
                throw new \InvalidArgumentException(
                    'Payment amount must be equal to the total order amount: ' . $totalAmount
                );
            }

            // Generate unique payment reference if not provided
            $params['payment_reference'] = $params['payment_reference']
                ?? 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

            // Create payment record
            $payment = Payment::create([
                'order_id'          => $order->id,
                'customer_id'       => $order->customer_id ?? 1,
                'service_name'      => $params['service_name'] ?? null,
                'amount_paid'       => $params['amount'],
                'payment_reference' => $params['payment_reference'],
                'payment_status'    => $params['payment_status'] ?? 'paid',
                'notes'             => $params['notes'] ?? null,
                'amount'            => $params['amount'],
                'payment_method'    => $params['payment_method'] ?? 'cash',
                'payment_date'      => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // Update order status to paid
            $order->status = true; // Assuming true means paid
            $order->save();

            DB::commit();
            return $payment;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());
            throw new \Exception('Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Create an invoice for an order and payment base on sale_type(ex:Material or Finished Good) and payment_status of order .
     *
     * @param array $params
     * @return \App\Models\Invoice
     * @throws \Exception
     */
    public function createInvoice(array $params = [])
    {
        try {
            DB::beginTransaction();

            // Validate required parameters
            if (empty($params['order_id']) || !is_numeric($params['order_id'])) {
                throw new \InvalidArgumentException('Order ID is required.');
            }

            // Find the order
            $order = Order::find($params['order_id']);
            if (!$order) {
                throw new \InvalidArgumentException('Order not found for ID: ' . $params['order_id']);
            }

            // Check if the order is already paid
            if ($order->status === true) {
                throw new \InvalidArgumentException('Order is already paid for ID: ' . $params['order_id']);
            }

            // Determine invoice details based on sale_type
            $saleType = $order->sale_type ?? 'POS';
            $invoiceData = [
                'order_id'     => $order->id,
                'customer_id'  => $order->customer_id ?? 1,
                'invoice_date' => now(),
                'due_date'     => now()->addDays(30),
                'status'       => 'pending',
                'created_at'   => now(),
                'updated_at'   => now(),
            ];

            // Calculate total_amount based on sale_type
            if ($saleType === 'Finished Good') {
                // For Finished Good, use total_price from order
                $invoiceData['total_amount'] = $order->total_price;
                $invoiceData['title'] = $order->title ?? null;
            } elseif ($saleType === 'Material') {
                // For Material, sum order_items total_price
                $invoiceData['total_amount'] = DB::table('order_items')
                    ->where('order_id', $order->id)
                    ->sum('total_price');
            } else {
                // Default fallback
                $invoiceData['total_amount'] = $order->total_price;
            }

            // Create invoice record
            $invoice = \App\Models\Invoice::create($invoiceData);

            DB::commit();
            return $invoice;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Invoice creation error: ' . $e->getMessage());
            throw new \Exception('Invoice creation failed: ' . $e->getMessage());
        }
    }
}
