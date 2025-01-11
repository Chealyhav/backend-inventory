<?php

use App\Http\Controllers\API\v1\TelegramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\ColorController;
use App\Http\Controllers\API\v1\ProductController;
use App\Http\Controllers\API\v1\CategoryController;
use App\Http\Controllers\Api\v1\ProductDetailController;
use App\Http\Controllers\API\v1\SubCategoryController;
use App\Http\Controllers\API\v1\SubProductController;
use App\Http\Controllers\API\v1\SaleController;
use App\Http\Controllers\API\v1\SaleDetailController;
use App\Http\Controllers\API\v1\StockController;
use App\Http\Controllers\API\v1\SubscriptionDetailController;
use App\Http\Controllers\API\v1\UserController;
use App\Http\Controllers\API\v1\RoleController;





Route::get('/categories', [CategoryController::class, 'index']); // List all categories
Route::get('/categories/{id}', [CategoryController::class, 'show']); // Get category by ID
Route::post('/categories', [CategoryController::class, 'store']); // Create a new category
Route::put('/categories/{id}', [CategoryController::class, 'update']); // Update a category
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']); // Soft delete a category
Route::patch('/categories/{id}/restore', [CategoryController::class, 'restore']); // Restore a category
Route::delete('/categories/{id}/force', [CategoryController::class, 'forceDelete']); // Permanently delete a category

Route::get('/colors', [ColorController::class, 'index']);
Route::post('/colors', [ColorController::class, 'store']);
Route::get('/colors/{id}', [ColorController::class, 'show']);
Route::put('/colors/{id}', [ColorController::class, 'update']);
Route::delete('/colors/{id}', [ColorController::class, 'destroy']);
Route::delete('colors/{id}/restore', [ColorController::class,'restore']);

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);


Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

Route::get('/subcategories', [SubCategoryController::class, 'index']);
Route::post('/subcategories', [SubCategoryController::class, 'store']);
Route::get('/subcategories/{id}', [SubCategoryController::class, 'show']);
Route::put('/subcategories/{id}', [SubCategoryController::class, 'update']);
Route::delete('/subcategories/{id}', [SubCategoryController::class, 'destroy']);
Route::post('subcategories/{id}/restore', [SubCategoryController::class, 'restore']);

Route::get('/subproducts', [SubProductController::class, 'index']);
Route::post('/subproducts', [SubProductController::class, 'store']);
Route::get('/subproducts/{id}', [SubProductController::class, 'show']);
Route::put('/subproducts/{id}', [SubProductController::class, 'update']);
Route::delete('/subproducts/{id}', [SubProductController::class, 'destroy']);

Route::get('/sales', [SaleController::class, 'index']);
Route::post('/sales', [SaleController::class, 'store']);
Route::get('/sales/{id}', [SaleController::class, 'show']);
Route::put('/sales/{id}', [SaleController::class, 'update']);
Route::delete('/sales/{id}', [SaleController::class, 'destroy']);

Route::get('/sale_details', [SaleDetailController::class, 'index']);
Route::post('/sale_details', [SaleDetailController::class,'store']);
Route::get('/sale_details/{id}', [SaleDetailController::class,'show']);
Route::put('/sale_details/{id}', [SaleDetailController::class,'update']);
Route::delete('/sale_details/{id}', [SaleDetailController::class,'destroy']);

Route::get('/stocks', [StockController::class, 'index']);

Route::get('/stocks/{id}', [StockController::class, 'show']);
Route::put('/stocks/{id}', [StockController::class, 'update']);
Route::delete('/stocks/{id}', [StockController::class, 'destroy']);

Route::post('/add_stocks', [StockController::class, 'addStock']);
Route::post('/subtract_stocks', [StockController::class, 'subtractStock']);


Route::get('/get_aluminum', [ProductDetailController::class,'getAluminum']);
Route::get('/get_accessories', [ProductDetailController::class,'getAccessories']);

Route::post('/telegram_bot', [TelegramController::class,'index']);



