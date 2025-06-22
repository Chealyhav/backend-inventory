<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/export/excel', [InvoiceController::class, 'exportExcel'])->name('export.excel');
Route::get('/{id}/export/pdf', [InvoiceController::class, 'exportPdf'])->name('export.pdf');
Route::get('/export/pdf', [InvoiceController::class, 'exportAllPdf'])->name('export.all-pdf');

