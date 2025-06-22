<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoiceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Invoice::with(['customer', 'sale', 'payment'])
            ->orderBy('invoice_date', 'desc')
            ->get();
    }

    /**
     * @param Invoice $invoice
     * @return array
     */
    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            $invoice->customer_name,
            $invoice->customer ? $invoice->customer->email : 'N/A',
            $invoice->customer ? $invoice->customer->phone : 'N/A',
            number_format($invoice->sub_total, 2),
            number_format($invoice->discount, 2),
            number_format($invoice->service_charge, 2),
            number_format($invoice->total_amount, 2),
            ucfirst(str_replace('_', ' ', $invoice->payment_status)),
            $invoice->payment_method,
            $invoice->invoice_date ? $invoice->invoice_date->format('M d, Y') : 'N/A',
            $invoice->notes ?? 'N/A',
            $invoice->createdBy ? $invoice->createdBy->name : 'N/A',
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Invoice Number',
            'Customer Name',
            'Customer Email',
            'Customer Phone',
            'Sub Total',
            'Discount',
            'Service Charge',
            'Total Amount',
            'Payment Status',
            'Payment Method',
            'Invoice Date',
            'Notes',
            'Created By'
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
