<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API Routes
Route::prefix('v1')->group(function () {
    // Products
    Route::get('/products', [ProductController::class, 'getAvailable']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    // Authenticated API Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Orders
        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

        // Inventory (Manager only)
        Route::get('/inventory/low-stock', [InventoryController::class, 'getLowStock']);
    });
});
