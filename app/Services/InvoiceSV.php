<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Payment;
use App\Exports\InvoiceExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoiceSV extends BaseService
{
    /**
     * Get all invoices with pagination and filters
     */
    public function index(Request $request)
    {
        // Mock data for testing
        $mockInvoices = [
            [
                'id' => 1,
                'invoice_number' => 'INV202501001',
                'invoice_date' => '2025-01-15',
                'total_amount' => 1500.00,
                'payment_status' => 'paid',
                'created_at' => '2025-01-15 10:30:00',
                'customer' => [
                    'id' => 1,
                    'name' => 'John Doe',
                    'email' => 'john@example.com'
                ],
                'sale' => [
                    'id' => 1,
                    'sale_number' => 'SALE202501001'
                ],
                'payment' => [
                    'id' => 1,
                    'payment_method' => 'Credit Card'
                ],
                'created_by' => [
                    'id' => 1,
                    'name' => 'Admin User'
                ]
            ],
            [
                'id' => 2,
                'invoice_number' => 'INV202501002',
                'invoice_date' => '2025-01-16',
                'total_amount' => 2300.50,
                'payment_status' => 'pending',
                'created_at' => '2025-01-16 14:20:00',
                'customer' => [
                    'id' => 2,
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com'
                ],
                'sale' => [
                    'id' => 2,
                    'sale_number' => 'SALE202501002'
                ],
                'payment' => [
                    'id' => 2,
                    'payment_method' => 'Pending'
                ],
                'created_by' => [
                    'id' => 1,
                    'name' => 'Admin User'
                ]
            ],
            [
                'id' => 3,
                'invoice_number' => 'INV202501003',
                'invoice_date' => '2025-01-17',
                'total_amount' => 890.75,
                'payment_status' => 'paid',
                'created_at' => '2025-01-17 09:15:00',
                'customer' => [
                    'id' => 3,
                    'name' => 'Bob Johnson',
                    'email' => 'bob@example.com'
                ],
                'sale' => [
                    'id' => 3,
                    'sale_number' => 'SALE202501003'
                ],
                'payment' => [
                    'id' => 3,
                    'payment_method' => 'Bank Transfer'
                ],
                'created_by' => [
                    'id' => 1,
                    'name' => 'Admin User'
                ]
            ],
            [
                'id' => 4,
                'invoice_number' => 'INV202501004',
                'invoice_date' => '2025-01-18',
                'total_amount' => 3200.00,
                'payment_status' => 'partially_paid',
                'created_at' => '2025-01-18 16:45:00',
                'customer' => [
                    'id' => 4,
                    'name' => 'Alice Brown',
                    'email' => 'alice@example.com'
                ],
                'sale' => [
                    'id' => 4,
                    'sale_number' => 'SALE202501004'
                ],
                'payment' => [
                    'id' => 4,
                    'payment_method' => 'Cash'
                ],
                'created_by' => [
                    'id' => 1,
                    'name' => 'Admin User'
                ]
            ],
            [
                'id' => 5,
                'invoice_number' => 'INV202501005',
                'invoice_date' => '2025-01-19',
                'total_amount' => 1750.25,
                'payment_status' => 'pending',
                'created_at' => '2025-01-19 11:30:00',
                'customer' => [
                    'id' => 5,
                    'name' => 'Charlie Wilson',
                    'email' => 'charlie@example.com'
                ],
                'sale' => [
                    'id' => 5,
                    'sale_number' => 'SALE202501005'
                ],
                'payment' => [
                    'id' => 5,
                    'payment_method' => 'Pending'
                ],
                'created_by' => [
                    'id' => 1,
                    'name' => 'Admin User'
                ]
            ]
        ];

        // Convert to objects to simulate Eloquent models
        $invoices = collect($mockInvoices)->map(function ($invoice) {
            return (object) $invoice;
        });

        // Simple pagination simulation
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        
        $paginatedInvoices = $invoices->slice($offset, $perPage);
        
        // Create a simple paginator
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedInvoices,
            $invoices->count(),
            $perPage,
            $page,
            ['path' => $request->url()]
        );

        return $paginator;
    }

    /**
     * Get invoice by ID
     */
    public function show($id)
    {
        // Mock data for testing
        $mockInvoice = [
            'id' => $id,
            'invoice_number' => 'INV202501' . str_pad($id, 3, '0', STR_PAD_LEFT),
            'invoice_date' => '2025-01-15',
            'due_date' => '2025-02-15',
            'total_amount' => 1500.00,
            'tax_amount' => 150.00,
            'subtotal' => 1350.00,
            'payment_status' => 'paid',
            'notes' => 'Thank you for your business!',
            'created_at' => '2025-01-15 10:30:00',
            'updated_at' => '2025-01-15 10:30:00',
            'customer' => [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '+1234567890',
                'address' => '123 Main St, City, State 12345'
            ],
            'sale' => [
                'id' => 1,
                'sale_number' => 'SALE202501001',
                'sale_date' => '2025-01-15'
            ],
            'payment' => [
                'id' => 1,
                'payment_method' => 'Credit Card',
                'payment_date' => '2025-01-15',
                'amount_paid' => 1500.00
            ],
            'created_by' => [
                'id' => 1,
                'name' => 'Admin User'
            ],
            'updated_by' => [
                'id' => 1,
                'name' => 'Admin User'
            ]
        ];

        return (object) $mockInvoice;
    }

    /**
     * Create new invoice
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id();
            $data['invoice_number'] = $this->generateInvoiceNumber();

            $invoice = Invoice::create($data);

            DB::commit();
            return $invoice->load(['customer', 'sale', 'payment']);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update invoice
     */
    public function update(array $params = array(), string $id = null)
    {
        DB::beginTransaction();
        try {
            $invoice = Invoice::findOrFail($id);
            $params['updated_by'] = Auth::id();

            $invoice->update($params);

            DB::commit();
            return $invoice->load(['customer', 'sale', 'payment']);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete invoice
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->deleted_by = Auth::id();
            $invoice->save();
            $invoice->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Restore deleted invoice
     */
    public function restore($id)
    {
        $invoice = Invoice::withTrashed()->findOrFail($id);
        $invoice->restore();
        return $invoice;
    }

    /**
     * Get invoice statistics
     */
    public function getStatistics()
    {
        // Mock data for testing
        return [
            'total_invoices' => 5,
            'paid_invoices' => 2,
            'pending_invoices' => 2,
            'partially_paid_invoices' => 1,
            'total_amount' => 9641.50,
            'paid_amount' => 2390.75,
            'pending_amount' => 4050.75,
            'partially_paid_amount' => 3200.00,
            'payment_rate' => 40.0
        ];
    }

    /**
     * Get monthly invoice data for charts
     */
    public function getMonthlyData()
    {
        // Mock data for testing
        return collect([
            [
                'year' => 2025,
                'month' => 1,
                'count' => 5,
                'total_amount' => 9641.50
            ],
            [
                'year' => 2025,
                'month' => 2,
                'count' => 3,
                'total_amount' => 4500.00
            ],
            [
                'year' => 2025,
                'month' => 3,
                'count' => 4,
                'total_amount' => 6200.75
            ]
        ]);
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');

        $lastInvoice = Invoice::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($id, $status)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->payment_status = $status;
        $invoice->updated_by = Auth::id();
        $invoice->save();

        return $invoice;
    }

    /**
     * Get invoices by customer
     */
    public function getByCustomer($customerId)
    {
        return Invoice::with(['customer', 'sale', 'payment'])
            ->where('customer_id', $customerId)
            ->orderBy('invoice_date', 'desc')
            ->get();
    }

    /**
     * Get recent invoices
     */
    public function getRecent($limit = 10)
    {
        // Mock data for testing
        $mockRecentInvoices = [
            [
                'id' => 1,
                'invoice_number' => 'INV202501001',
                'invoice_date' => '2025-01-19',
                'total_amount' => 1750.25,
                'payment_status' => 'pending',
                'created_at' => '2025-01-19 11:30:00',
                'customer' => [
                    'id' => 5,
                    'name' => 'Charlie Wilson',
                    'email' => 'charlie@example.com'
                ]
            ],
            [
                'id' => 2,
                'invoice_number' => 'INV202501002',
                'invoice_date' => '2025-01-18',
                'total_amount' => 3200.00,
                'payment_status' => 'partially_paid',
                'created_at' => '2025-01-18 16:45:00',
                'customer' => [
                    'id' => 4,
                    'name' => 'Alice Brown',
                    'email' => 'alice@example.com'
                ]
            ],
            [
                'id' => 3,
                'invoice_number' => 'INV202501003',
                'invoice_date' => '2025-01-17',
                'total_amount' => 890.75,
                'payment_status' => 'paid',
                'created_at' => '2025-01-17 09:15:00',
                'customer' => [
                    'id' => 3,
                    'name' => 'Bob Johnson',
                    'email' => 'bob@example.com'
                ]
            ],
            [
                'id' => 4,
                'invoice_number' => 'INV202501004',
                'invoice_date' => '2025-01-16',
                'total_amount' => 2300.50,
                'payment_status' => 'pending',
                'created_at' => '2025-01-16 14:20:00',
                'customer' => [
                    'id' => 2,
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com'
                ]
            ],
            [
                'id' => 5,
                'invoice_number' => 'INV202501005',
                'invoice_date' => '2025-01-15',
                'total_amount' => 1500.00,
                'payment_status' => 'paid',
                'created_at' => '2025-01-15 10:30:00',
                'customer' => [
                    'id' => 1,
                    'name' => 'John Doe',
                    'email' => 'john@example.com'
                ]
            ]
        ];

        // Convert to objects and limit
        $invoices = collect($mockRecentInvoices)
            ->take($limit)
            ->map(function ($invoice) {
                return (object) $invoice;
            });

        return $invoices;
    }

    /**
     * Export invoices to Excel
     */
    public function exportExcel(): BinaryFileResponse
    {
        // Mock data for testing
        $mockData = [
            [
                'invoice_number' => 'INV202501001',
                'customer_name' => 'John Doe',
                'customer_email' => 'john@example.com',
                'invoice_date' => '2025-01-15',
                'total_amount' => 1500.00,
                'payment_status' => 'paid',
                'created_at' => '2025-01-15 10:30:00'
            ],
            [
                'invoice_number' => 'INV202501002',
                'customer_name' => 'Jane Smith',
                'customer_email' => 'jane@example.com',
                'invoice_date' => '2025-01-16',
                'total_amount' => 2300.50,
                'payment_status' => 'pending',
                'created_at' => '2025-01-16 14:20:00'
            ],
            [
                'invoice_number' => 'INV202501003',
                'customer_name' => 'Bob Johnson',
                'customer_email' => 'bob@example.com',
                'invoice_date' => '2025-01-17',
                'total_amount' => 890.75,
                'payment_status' => 'paid',
                'created_at' => '2025-01-17 09:15:00'
            ],
            [
                'invoice_number' => 'INV202501004',
                'customer_name' => 'Alice Brown',
                'customer_email' => 'alice@example.com',
                'invoice_date' => '2025-01-18',
                'total_amount' => 3200.00,
                'payment_status' => 'partially_paid',
                'created_at' => '2025-01-18 16:45:00'
            ],
            [
                'invoice_number' => 'INV202501005',
                'customer_name' => 'Charlie Wilson',
                'customer_email' => 'charlie@example.com',
                'invoice_date' => '2025-01-19',
                'total_amount' => 1750.25,
                'payment_status' => 'pending',
                'created_at' => '2025-01-19 11:30:00'
            ]
        ];

        return Excel::download(new InvoiceExport, 'invoices-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export single invoice to PDF
     */
    public function exportPdf($id): \Illuminate\Http\Response
    {
        // Mock data for testing
        $mockInvoice = [
            'id' => $id,
            'invoice_number' => 'INV202501' . str_pad($id, 3, '0', STR_PAD_LEFT),
            'invoice_date' => '2025-01-15',
            'due_date' => '2025-02-15',
            'total_amount' => 1500.00,
            'tax_amount' => 150.00,
            'subtotal' => 1350.00,
            'payment_status' => 'paid',
            'notes' => 'Thank you for your business!',
            'customer' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '+1234567890',
                'address' => '123 Main St, City, State 12345'
            ],
            'sale' => [
                'sale_number' => 'SALE202501001',
                'sale_date' => '2025-01-15'
            ],
            'payment' => [
                'payment_method' => 'Credit Card',
                'payment_date' => '2025-01-15',
                'amount_paid' => 1500.00
            ],
            'created_by' => [
                'name' => 'Admin User'
            ],
            'items' => [
                [
                    'product_name' => 'Product A',
                    'quantity' => 2,
                    'unit_price' => 500.00,
                    'total' => 1000.00
                ],
                [
                    'product_name' => 'Product B',
                    'quantity' => 1,
                    'unit_price' => 350.00,
                    'total' => 350.00
                ]
            ]
        ];

        $invoice = (object) $mockInvoice;
        $pdf = Pdf::loadView('exports.invoice_pdf', compact('invoice'));
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Export all invoices to PDF
     */
    public function exportAllPdf(): \Illuminate\Http\Response
    {
        // Mock data for testing
        $mockInvoices = [
            [
                'id' => 1,
                'invoice_number' => 'INV202501001',
                'invoice_date' => '2025-01-15',
                'total_amount' => 1500.00,
                'payment_status' => 'paid',
                'customer' => ['name' => 'John Doe', 'email' => 'john@example.com'],
                'sale' => ['sale_number' => 'SALE202501001'],
                'payment' => ['payment_method' => 'Credit Card'],
                'created_by' => ['name' => 'Admin User']
            ],
            [
                'id' => 2,
                'invoice_number' => 'INV202501002',
                'invoice_date' => '2025-01-16',
                'total_amount' => 2300.50,
                'payment_status' => 'pending',
                'customer' => ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
                'sale' => ['sale_number' => 'SALE202501002'],
                'payment' => ['payment_method' => 'Pending'],
                'created_by' => ['name' => 'Admin User']
            ],
            [
                'id' => 3,
                'invoice_number' => 'INV202501003',
                'invoice_date' => '2025-01-17',
                'total_amount' => 890.75,
                'payment_status' => 'paid',
                'customer' => ['name' => 'Bob Johnson', 'email' => 'bob@example.com'],
                'sale' => ['sale_number' => 'SALE202501003'],
                'payment' => ['payment_method' => 'Bank Transfer'],
                'created_by' => ['name' => 'Admin User']
            ],
            [
                'id' => 4,
                'invoice_number' => 'INV202501004',
                'invoice_date' => '2025-01-18',
                'total_amount' => 3200.00,
                'payment_status' => 'partially_paid',
                'customer' => ['name' => 'Alice Brown', 'email' => 'alice@example.com'],
                'sale' => ['sale_number' => 'SALE202501004'],
                'payment' => ['payment_method' => 'Cash'],
                'created_by' => ['name' => 'Admin User']
            ],
            [
                'id' => 5,
                'invoice_number' => 'INV202501005',
                'invoice_date' => '2025-01-19',
                'total_amount' => 1750.25,
                'payment_status' => 'pending',
                'customer' => ['name' => 'Charlie Wilson', 'email' => 'charlie@example.com'],
                'sale' => ['sale_number' => 'SALE202501005'],
                'payment' => ['payment_method' => 'Pending'],
                'created_by' => ['name' => 'Admin User']
            ]
        ];

        $invoices = collect($mockInvoices)->map(function ($invoice) {
            return (object) $invoice;
        });

        $pdf = Pdf::loadView('exports.invoices_pdf', compact('invoices'));
        return $pdf->download('all-invoices-' . date('Y-m-d') . '.pdf');
    }
}
