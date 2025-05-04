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
use App\Http\Controllers\Api\v1\CloudinaryController;
use App\Http\Controllers\Api\v1\ProductExportController;
use App\Http\Controllers\Api\v1\CustomerController;


Route::group(['middleware' => 'auth:api', 'prefix' => 'auth/v1'], function ($router) {


    ################# user management ######################

    //authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refreshToken']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    //user management
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::post('/users/{id}/restore', [UserController::class, 'restore']);
    //roles management
    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
    //permissions management
    Route::get('/permissions', [RoleController::class, 'index']);
    Route::post('/permissions', [RoleController::class, 'store']);
    Route::get('/permissions/{id}', [RoleController::class, 'show']);
    Route::put('/permissions/{id}', [RoleController::class, 'update']);
    Route::delete('/permissions/{id}', [RoleController::class, 'destroy']);


    ######### inventory management #########################

    //categories for products
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    //subcategories for products
    Route::get('/subcategories', [SubCategoryController::class, 'index']);
    Route::post('/subcategories', [SubCategoryController::class, 'store']);
    Route::get('/subcategories/{id}', [SubCategoryController::class, 'show']);
    Route::put('/subcategories/{id}', [SubCategoryController::class, 'update']);
    Route::delete('/subcategories/{id}', [SubCategoryController::class, 'destroy']);
    Route::post('subcategories/{id}/restore', [SubCategoryController::class, 'restore']);

    //colors for products
    Route::get('/colors', [ColorController::class, 'index']);
    Route::post('/colors', [ColorController::class, 'store']);
    Route::get('/colors/{id}', [ColorController::class, 'show']);
    Route::put('/colors/{id}', [ColorController::class, 'update']);
    Route::delete('/colors/{id}', [ColorController::class, 'destroy']);
    Route::delete('colors/{id}/restore', [ColorController::class, 'restore']);

    //products for inventory
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    //subproducts for inventory
    Route::get('/subproducts', [SubProductController::class, 'index']);
    Route::post('/subproducts', [SubProductController::class, 'store']);
    Route::get('/subproducts/{id}', [SubProductController::class, 'show']);
    Route::put('/subproducts/{id}', [SubProductController::class, 'update']);
    Route::delete('/subproducts/{id}', [SubProductController::class, 'destroy']);

    //product details for inventory
    Route::get('/get_aluminum', [ProductDetailController::class, 'getAluminum']);
    Route::get('/get_accessories', [ProductDetailController::class, 'getAccessories']);

    //sales for inventory
    Route::get('/sales', [SaleController::class, 'index']);
    Route::get('/sales/{id}', [SaleController::class, 'show']);
    //Route::post('/sales', [SaleController::class, 'store']);
    // Route::put('/sales/{id}', [SaleController::class, 'update']);
    // Route::delete('/sales/{id}', [SaleController::class, 'destroy']);
    //sale details for inventory
    Route::get('/sale_details', [SaleDetailController::class, 'index']);
    Route::get('/sale_details/{id}', [SaleDetailController::class, 'show']);
    // Route::post('/sale_details', [SaleDetailController::class, 'store']);
    // Route::put('/sale_details/{id}', [SaleDetailController::class, 'update']);
    // Route::delete('/sale_details/{id}', [SaleDetailController::class, 'destroy']);

    //stock management
    Route::get('/stocks', [StockController::class, 'index']);
    Route::get('/stocks/{id}', [StockController::class, 'show']);
    Route::put('/stocks/{id}', [StockController::class, 'update']);
    Route::delete('/stocks/{id}', [StockController::class, 'destroy']);
    Route::post('/add_stocks', [StockController::class, 'addStock']);
    //deduct stocks by STOCKER
    Route::post('/subtract_stocks', [StockController::class, 'subtractStock']);
    ################ POS management ######################
    //sale transaction and order management
    Route::post('/transaction', [SaleController::class, 'saleTransaction']); // Sale transaction
    Route::post('/sales', [SaleController::class, 'store']); // Create a sale
    Route::post('/add-items', [SaleController::class, 'storeItems']); // Store sale items
    //payment management
    Route::get('/payment', [SaleController::class, 'indexPayment']); // List all payments
    Route::get('/payment/{id}', [SaleController::class, 'showPayment']); // Get payment by ID
    Route::put('/payment/{id}', [SaleController::class, 'updatePayment']); // Update payment
    Route::delete('/payment/{id}', [SaleController::class, 'destroyPayment']); // Delete payment
    // order management
    Route::get('/orders', [SaleController::class, 'indexOrder']); // List all orders
    Route::get('/orders/{id}', [SaleController::class, 'showOrder']); // Get order by ID
    Route::put('/orders/{id}', [SaleController::class, 'updateOrder']); // Update order
    Route::delete('/orders/{id}', [SaleController::class, 'destroyOrder']); // Delete order
    //customer management
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
    Route::post('/customers/{id}/restore', [CustomerController::class, 'restore']);


    ############# dashboard management ######################
    //dashboard for sale management




    //report management
    Route::post('/telegram_bot', [TelegramController::class, 'index']);
    //upload Image to cloudinary
    Route::post('/upload', [CloudinaryController::class, 'uploadImage']);
    Route::delete('/delete', [CloudinaryController::class, 'deleteImage']);
    Route::get('/image-url', [CloudinaryController::class, 'getImageUrl']);
});

Route::get('/customers', [CustomerController::class, 'index']);
Route::post('/customers', [CustomerController::class, 'store']);
Route::put('/customers/{id}', [CustomerController::class, 'update']);
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);


//Login and Register
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refreshToken']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);




