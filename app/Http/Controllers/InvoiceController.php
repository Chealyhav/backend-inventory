<?php

namespace App\Http\Controllers;

use App\Services\InvoiceSV;
use App\Http\Requests\InvoiceRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    protected InvoiceSV $invoiceService;

    public function __construct(InvoiceSV $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display the invoice dashboard
     */
    public function index(Request $request): View
    {
        $invoices = $this->invoiceService->index($request);
        $statistics = $this->invoiceService->getStatistics();
        $recentInvoices = $this->invoiceService->getRecent(5);

        return view('invoices.index', compact('invoices', 'statistics', 'recentInvoices'));
    }

    /**
     * Show the form for creating a new invoice
     */
    public function create(): View
    {
        return view('invoices.create');
    }

    /**
     * Store a newly created invoice
     */
    public function store(InvoiceRequest $request)
    {
        try {
            $invoice = $this->invoiceService->store($request);
            return redirect()->route('invoices.show', $invoice->id)
                           ->with('success', 'Invoice created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified invoice
     */
    public function show($id): View
    {
        $invoice = $this->invoiceService->show($id);
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice
     */
    public function edit($id): View
    {
        $invoice = $this->invoiceService->show($id);
        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified invoice
     */
    public function update(InvoiceRequest $request, $id)
    {
        try {
            $invoice = $this->invoiceService->update($request->validated(), $id);
            return redirect()->route('invoices.show', $invoice->id)
                           ->with('success', 'Invoice updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified invoice
     */
    public function destroy($id)
    {
        try {
            $this->invoiceService->destroy($id);
            return redirect()->route('invoices.index')
                           ->with('success', 'Invoice deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export invoices to Excel
     */
    public function exportExcel()
    {
        try {
            return $this->invoiceService->exportExcel();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export single invoice to PDF
     */
    public function exportPdf($id)
    {
        try {
            return $this->invoiceService->exportPdf($id);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export all invoices to PDF
     */
    public function exportAllPdf()
    {
        try {
            return $this->invoiceService->exportAllPdf();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show invoice statistics
     */
    public function statistics(): View
    {
        $statistics = $this->invoiceService->getStatistics();
        $monthlyData = $this->invoiceService->getMonthlyData();

        return view('invoices.statistics', compact('statistics', 'monthlyData'));
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'payment_status' => 'required|in:pending,paid,partially_paid,cancelled'
            ]);

            $invoice = $this->invoiceService->updatePaymentStatus($id, $request->payment_status);
            return back()->with('success', 'Payment status updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
