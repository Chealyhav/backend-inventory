<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ProductsExport implements FromCollection, WithHeadings, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return new Collection($this->data);
    }

    public function headings(): array
    {
        return [
            'Nº',
            'SN',
            'Image',
            'Product Name',
            'Code',
            'Color',
            'Package',
            'Length (mm)',
            'Thickness (mm)',
            'Weight per Unit (kg)',
            'Total Weight (kg)',
            'Buy Price',
            'Sell Price',
            'In',
            'Out',
            'Stock',
            'Type',
            'Remarks'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F4F4F4']
                ]
            ],
        ];
    }
}
