<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;

class ExportService
{
    protected $productDetailSV;

    public function __construct(ProductDetailSV $productDetailSV)
    {
        $this->productDetailSV = $productDetailSV;
    }

    public function exportToPDF($params)
    {
        $products = $this->getProductsForExport($params);
        $data = $this->formatProductsForExport($products);

        $pdf = Pdf::loadView('exports.products_pdf', [
            'data' => $data,
            'title' => 'Products Report',
            'date' => now()->format('Y-m-d H:i:s')
        ]);

        return $pdf->download('products_report.pdf');
    }

    public function exportToExcel($params)
    {
        $products = $this->getProductsForExport($params);
        $data = $this->formatProductsForExport($products);

        return Excel::download(new ProductsExport($data), 'products_report.xlsx');
    }

    private function getProductsForExport($params)
    {
        if ($params['category'] == 1) {
            return $this->productDetailSV->getAccessoriesProductsByCategory($params);
        }
        return $this->productDetailSV->getAllAluminumProductsByCategory($params);
    }

    private function formatProductsForExport($products)
    {
        $formattedData = [];
        $sn = 1;

        foreach ($products as $product) {
            foreach ($product['products'] as $subProduct) {
                $formattedData[] = [
                    'SN' => $sn++,
                    'Product Code' => $product['productCode'],
                    'Image' => $product['productImage'] ?? 'No Image',
                    'Product Name' => $product['productName'],
                    'Code' => $subProduct['code'],
                    'Color' => $subProduct['color'],
                    'Package' => $subProduct['pieces'] ?? 0,
                    'Length (mm)' => $subProduct['length'] ?? 0,
                    'Thickness (mm)' => $subProduct['thickness'] ?? 0,
                    'Weight per Unit (kg)' => $subProduct['unitWidth'] ?? 0,
                    'Total Weight (kg)' => $subProduct['totalWeight'] ?? '-',
                    'Buy Price' => '$' . number_format($subProduct['buyPrice'], 2),
                    'Sell Price' => '$' . number_format($subProduct['sellPrice'], 2),
                    'Stock In' => $subProduct['stockIn'] ?? 0,
                    'Stock Out' => $subProduct['stockOut'] ?? 0,
                    'Stock' => $subProduct['currentStock'] ?? 0,
                    'Type' => $product['stockType'] ?? 'PCS',
                    'Remarks' => $subProduct['remarks'] ?? 'InStock'
                ];
            }
        }

        return $formattedData;
    }
}
