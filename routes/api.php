<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// //test
// Route::get('/test' , function () {
//     return response()->json([
//         'message' => 'Hello World',
//     ]);
// });

use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleDetailController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\SubProductController;
use App\Http\Controllers\StockController;

Route::resource('products', ProductController::class);
Route::resource('sales', SaleController::class);
Route::resource('sale-details', SaleDetailController::class);
Route::resource('customers', CustomerController::class);
Route::resource('categories', CategoryController::class);
Route::resource('subcategories', SubCategoryController::class);
Route::resource('colors', ColorController::class);
Route::resource('subproducts', SubProductController::class);
Route::resource('stocks', StockController::class);

