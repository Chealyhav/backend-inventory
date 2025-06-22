<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\v1\BaseAPI;
use App\Services\InvoiceSV;
use App\Http\Requests\InvoiceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvoiceController extends BaseAPI
{
    protected InvoiceSV $invoiceService;

    public function __construct(InvoiceSV $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Get all invoices with pagination and filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $invoices = $this->invoiceService->index($request);
            return $this->successResponse($invoices, 'Invoices retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get invoice by ID
     */
    public function show($id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->show($id);
            return $this->successResponse($invoice, 'Invoice retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    /**
     * Create new invoice
     */
    public function store(InvoiceRequest $request): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->store($request);
            return $this->successResponse($invoice, 'Invoice created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update invoice
     */
    public function update(InvoiceRequest $request, $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->update($request->validated(), $id);
            return $this->successResponse($invoice, 'Invoice updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Delete invoice
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->invoiceService->destroy($id);
            return $this->successResponse(null, 'Invoice deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Restore deleted invoice
     */
    public function restore($id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->restore($id);
            return $this->successResponse($invoice, 'Invoice restored successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
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
            return $this->errorResponse($e->getMessage(), 500);
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
            return $this->errorResponse($e->getMessage(), 500);
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
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get invoice statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->invoiceService->getStatistics();
            return $this->successResponse($statistics, 'Statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get monthly invoice data for charts
     */
    public function monthlyData(): JsonResponse
    {
        try {
            $data = $this->invoiceService->getMonthlyData();
            return $this->successResponse($data, 'Monthly data retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'payment_status' => 'required|in:pending,paid,partially_paid,cancelled'
            ]);

            $invoice = $this->invoiceService->updatePaymentStatus($id, $request->payment_status);
            return $this->successResponse($invoice, 'Payment status updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get invoices by customer
     */
    public function getByCustomer($customerId): JsonResponse
    {
        try {
            $invoices = $this->invoiceService->getByCustomer($customerId);
            return $this->successResponse($invoices, 'Customer invoices retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get recent invoices
     */
    public function recent(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $invoices = $this->invoiceService->getRecent($limit);
            return $this->successResponse($invoices, 'Recent invoices retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
