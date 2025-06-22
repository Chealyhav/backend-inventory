<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

use App\Services\ReportService;
use App\Exports\UsersExport;

class ExportpdfController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Export users as PDF
     */
    public function exportUsersPdf(): BinaryFileResponse
    {
        // Get mock data
        $users = $this->reportService->getMockUserData();

        // Generate PDF from Blade view
        $pdf = $this->reportService->generatePdf('pdf.user_report', ['users' => $users]);

        // Save PDF to temporary file
        $tempPath = tempnam(sys_get_temp_dir(), 'user_report_') . '.pdf';
        file_put_contents($tempPath, $pdf->output());

        // Return downloadable file
        return response()->download($tempPath, 'user_report.pdf')->deleteFileAfterSend(true);
    }

    /**
     * Export users as Excel (XLSX)
     */
    public function exportUsersExcel(): BinaryFileResponse
    {
        // Use Laravel Excel export class
        return Excel::download(new UsersExport(), 'user_report.xlsx');
    }
}