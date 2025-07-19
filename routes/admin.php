<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CartController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Products Management
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/upload-images', [ProductController::class, 'uploadImages'])->name('products.upload-images');
    
    // Orders Management  
    Route::resource('orders', OrderController::class);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    
    // Cart Management
    Route::get('carts', [CartController::class, 'index'])->name('carts.index');
    Route::delete('carts/{cart}', [CartController::class, 'destroy'])->name('carts.destroy');
});
