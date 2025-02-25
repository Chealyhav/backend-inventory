<?php
namespace App\Http\Controllers\Api\v1;

use App\Exports\ProductExcelExport;
use App\Exports\ProductPdfExport;
use App\Services\ProductDetailSV;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ProductExportController extends BaseAPI
{
    protected $productService;

    public function __construct(ProductDetailSV $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Export products as an Excel file
     */
    public function exportExcel()
    {
        try {
            $params = request()->all(); // Get query parameters
            $formattedData = $this->productService->getAllAluminumProductsByCategory($params);
            return Excel::download(new ProductExcelExport($formattedData), 'products_report.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Export products as a PDF file
     */
    public function exportPdf()
    {
        try {
            $params = request()->all(); // Get query parameters
            $formattedData = $this->productService->getAllAluminumProductsByCategory($params);

            // Check if formattedData is not empty
            if (empty($formattedData)) {
                return $this->errorResponse('No products found.');
            }

            // Create PDF with the data
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.products', ['products' => $formattedData])
                ->setPaper('a4', 'portrait');
    
            return $pdf->download('products_report.pdf');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }




}
