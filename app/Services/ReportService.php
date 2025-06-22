<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportService
{
    /**
     * Generate PDF from blade view with given data
     *
     * @param string $viewName
     * @param array $data
     * @return mixed
     */
    public function generatePdf(string $viewName, array $data)
    {
        return Pdf::loadView($viewName, $data);
    }

    /**
     * Get dummy/mock user data for testing
     *
     * @return array
     */
    public function getMockUserData(): array
    {
        return [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'created_at' => '2024-01-01'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'created_at' => '2024-01-05'],
            ['id' => 3, 'name' => 'Mike Johnson', 'email' => 'mike@example.com', 'created_at' => '2024-01-10'],
        ];
    }

    public function exportToExcel(string $viewName, array $data, string $fileName = 'report.xlsx'): string
    {
        $html = View::make($viewName, $data)->render();

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($html);
        $writer = new Xlsx($spreadsheet);

        $tempPath = tempnam(sys_get_temp_dir(), 'excel_') . '.xlsx';
        $writer->save($tempPath);

        return $tempPath;
    }
}