<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing customers, sales, and users
        $customers = Customer::all();
        $sales = Sale::all();
        $users = User::all();

        if ($customers->isEmpty() || $sales->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Skipping invoice seeding: No customers, sales, or users found.');
            return;
        }

        $paymentStatuses = ['pending', 'paid', 'partially_paid', 'cancelled'];
        $paymentMethods = ['cash', 'card', 'bank_transfer', 'check'];
        $serviceNames = ['Installation', 'Delivery', 'Consultation', 'Maintenance', 'Repair'];

        for ($i = 1; $i <= 50; $i++) {
            $customer = $customers->random();
            $sale = $sales->random();
            $user = $users->random();

            $subTotal = rand(100, 5000);
            $discount = rand(0, $subTotal * 0.2); // 0-20% discount
            $serviceCharge = rand(0, 200);
            $totalAmount = $subTotal - $discount + $serviceCharge;

            $invoiceDate = Carbon::now()->subDays(rand(0, 365));

            Invoice::create([
                'invoice_number' => 'INV' . date('Y') . date('m') . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'updated_price' => rand(0, 100),
                'sale_id' => $sale->id,
                'total_price' => $subTotal,
                'sub_total' => $subTotal,
                'discount' => $discount,
                'service_charge' => $serviceCharge,
                'service_name' => rand(0, 1) ? $serviceNames[array_rand($serviceNames)] : null,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                'total_amount' => $totalAmount,
                'payment_id' => null, // You can add payment relationships if needed
                'notes' => rand(0, 1) ? 'Sample invoice note for testing purposes.' : null,
                'invoice_date' => $invoiceDate,
                'status' => true,
                'created_by' => $user->id,
                'updated_by' => null,
                'deleted_by' => null,
                'created_at' => $invoiceDate,
                'updated_at' => $invoiceDate,
            ]);
        }

        $this->command->info('50 sample invoices created successfully!');
    }
}
