<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\TelegramController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ColorController;
use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\CategoryController;
use App\Http\Controllers\Api\v1\ProductDetailController;
use App\Http\Controllers\Api\v1\SubCategoryController;
use App\Http\Controllers\Api\v1\SubProductController;
use App\Http\Controllers\Api\v1\SaleController;
use App\Http\Controllers\Api\v1\SaleDetailController;
use App\Http\Controllers\Api\v1\StockController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\RoleController;
use App\Http\Controllers\Api\CloudinaryController;
use App\Http\Controllers\Api\ProductExportController;
use App\Http\Controllers\Api\ExportController;




Route::group(['middleware' => 'auth:api', 'prefix' => 'auth/v1'], function ($router) {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refreshToken']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);

    //   // Create a new sale
    //   Route::post('/sales', [SaleController::class, 'store']);
    //   // Add items to an existing sale
    //   Route::post('/sales/{sale_id}/items', [SaleController::class, 'storeItems']);
    //   // Process payment for a sale
    //   Route::post('/sales/{sale_id}/payment', [SaleController::class, 'processPayment']);

    Route::post('/transaction', [SaleController::class, 'saleTransaction']); // Sale transaction
    Route::post('/sales', [SaleController::class, 'store']); // Create a sale
    Route::post('/add-items', [SaleController::class, 'storeItems']); // Store sale items

    Route::post('/process-payment', [SaleController::class, 'processPayment']); // Process payment


    Route::post('/upload', [CloudinaryController::class, 'uploadImage']);
    Route::delete('/delete', [CloudinaryController::class, 'deleteImage']);
    Route::get('/image-url', [CloudinaryController::class, 'getImageUrl']);

    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);



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
    Route::delete('colors/{id}/restore', [ColorController::class, 'restore']);




    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products/{id}', [ProductController::class, 'update']);
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
    Route::post('/sale_details', [SaleDetailController::class, 'store']);
    Route::get('/sale_details/{id}', [SaleDetailController::class, 'show']);
    Route::put('/sale_details/{id}', [SaleDetailController::class, 'update']);
    Route::delete('/sale_details/{id}', [SaleDetailController::class, 'destroy']);

    Route::get('/stocks', [StockController::class, 'index']);

    Route::get('/stocks/{id}', [StockController::class, 'show']);
    Route::put('/stocks/{id}', [StockController::class, 'update']);
    Route::delete('/stocks/{id}', [StockController::class, 'destroy']);

    Route::post('/add_stocks', [StockController::class, 'addStock']);
    Route::post('/subtract_stocks', [StockController::class, 'subtractStock']);


    Route::get('/get_aluminum', [ProductDetailController::class, 'getAluminum']);
    Route::get('/get_accessories', [ProductDetailController::class, 'getAccessories']);

    Route::post('/telegram_bot', [TelegramController::class, 'index']);
});

Route::prefix('exports')->group(function () {
    Route::post('pdf', [ExportController::class, 'exportPDF']);
    Route::post('excel', [ExportController::class, 'exportExcel']);
});








// //test route
// Route::get('/test', function () {
//     return response()->json(['message' => 'Test route is working!']);
// });

//Login and Register
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/subproducts', [SubProductController::class, 'index']);
Route::post('/subproducts', [SubProductController::class, 'store']);
Route::get('/subproducts/{id}', [SubProductController::class, 'show']);
Route::put('/subproducts/{id}', [SubProductController::class, 'update']);
Route::delete('/subproducts/{id}', [SubProductController::class, 'destroy']);
// Route::delete('/subproducts/{id}', [SubProductController::class, 'forceDelete']);
Route::post('subproducts/{id}/restore', [SubProductController::class,'restore']);




Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/refresh', [AuthController::class, 'refreshToken']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);

//   // Create a new sale
//   Route::post('/sales', [SaleController::class, 'store']);
//   // Add items to an existing sale
//   Route::post('/sales/{sale_id}/items', [SaleController::class, 'storeItems']);
//   // Process payment for a sale
//   Route::post('/sales/{sale_id}/payment', [SaleController::class, 'processPayment']);

Route::post('/transaction', [SaleController::class, 'saleTransaction']); // Sale transaction
Route::post('/sales', [SaleController::class, 'store']); // Create a sale
Route::post('/add-items', [SaleController::class, 'storeItems']); // Store sale items

Route::post('/process-payment', [SaleController::class, 'processPayment']); // Process payment


Route::post('/upload', [CloudinaryController::class, 'uploadImage']);
Route::delete('/delete', [CloudinaryController::class, 'deleteImage']);
Route::get('/image-url', [CloudinaryController::class, 'getImageUrl']);

Route::get('/roles', [RoleController::class, 'index']);
Route::post('/roles', [RoleController::class, 'store']);
Route::get('/roles/{id}', [RoleController::class, 'show']);
Route::put('/roles/{id}', [RoleController::class, 'update']);
Route::delete('/roles/{id}', [RoleController::class, 'destroy']);



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
Route::delete('colors/{id}/restore', [ColorController::class, 'restore']);

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/products/{id}', [ProductController::class, 'update']);
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
Route::post('/sale_details', [SaleDetailController::class, 'store']);
Route::get('/sale_details/{id}', [SaleDetailController::class, 'show']);
Route::put('/sale_details/{id}', [SaleDetailController::class, 'update']);
Route::delete('/sale_details/{id}', [SaleDetailController::class, 'destroy']);

Route::get('/stocks', [StockController::class, 'index']);

Route::get('/stocks/{id}', [StockController::class, 'show']);
Route::put('/stocks/{id}', [StockController::class, 'update']);
Route::delete('/stocks/{id}', [StockController::class, 'destroy']);

Route::post('/add_stocks', [StockController::class, 'addStock']);
Route::post('/subtract_stocks', [StockController::class, 'subtractStock']);


Route::get('/get_aluminum', [ProductDetailController::class, 'getAluminum']);
Route::get('/get_accessories', [ProductDetailController::class, 'getAccessories']);

Route::post('/telegram_bot', [TelegramController::class, 'index']);
