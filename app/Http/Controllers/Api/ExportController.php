<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExportService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function exportPDF(Request $request)
    {
        try {
            $params = $request->validate([
                'category' => 'required|integer',
                'search' => 'nullable|string',
                'sortBy' => 'nullable|string',
                'sortOrder' => 'nullable|in:asc,desc',
            ]);

            return $this->exportService->exportToPDF($params);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $params = $request->validate([
                'category' => 'required|integer',
                'search' => 'nullable|string',
                'sortBy' => 'nullable|string',
                'sortOrder' => 'nullable|in:asc,desc',
            ]);

            return $this->exportService->exportToExcel($params);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Excel: ' . $e->getMessage()
            ], 500);
        }
    }
}
