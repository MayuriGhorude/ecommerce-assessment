<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;

// Test routes
Route::get('/test-simple', function() {
    return response()->json(['message' => 'Simple GET test working']);
});

Route::post('/test-cart', function(Request $request) {
    return response()->json([
        'success' => true,
        'received_data' => $request->all(),
        'method' => $request->method(),
        'message' => 'Test POST working'
    ]);
});

// Products routes
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

// Cart routes (THIS WAS MISSING!)
Route::post('/cart/add', [CartController::class, 'addToCart']);
Route::get('/cart', [CartController::class, 'getCartItems']);
Route::put('/cart/{id}', [CartController::class, 'updateCartItem']);
Route::delete('/cart/{id}', [CartController::class, 'removeFromCart']);

// Orders routes
Route::post('/orders/checkout', [OrderController::class, 'checkout']);
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
