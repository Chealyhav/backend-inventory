<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductPdfExport implements FromView
{
    protected $products;

    public function __construct($products)
    {
        // Convert collection to an array if necessary
        $this->products = collect($products)->map(function ($product) {
            return is_object($product) ? (array) $product : $product;
        })->toArray();
    }

    public function view(): View
    {
        return view('exports.products', ['products' => $this->products]);
    }
}
