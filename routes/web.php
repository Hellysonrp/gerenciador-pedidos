<?php

use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

// Products Routes (Full CRUD)
Route::controller(ProductsController::class)->group(function () {
    // Display listing
    Route::get('products', 'index')->name('products.index');

    // Show create form
    Route::get('products/create', 'create')->name('products.create');

    // Store new product
    Route::post('products', 'store')->name('products.store');

    // // Show single product (if needed)
    // Route::get('products/{product}', 'show')->name('products.show');

    // Show edit form
    Route::get('products/{product}/edit', 'edit')->name('products.edit');

    // Update product
    Route::put('products/{product}', 'update')->name('products.update');

    // Delete product
    Route::delete('products/{product}', 'delete')->name('products.delete');
});

// Orders Routes (CRUD without edit)
Route::controller(OrdersController::class)->group(function () {
    Route::get('orders', 'index')->name('orders.index');
    Route::get('orders/create', 'create')->name('orders.create');
    Route::post('orders', 'store')->name('orders.store');
    Route::get('orders/{order}', 'show')->name('orders.show');
    Route::put('orders/{order}', 'update')->name('orders.update');
    Route::delete('orders/{order}', 'delete')->name('orders.delete');
});

// Redirect root to products
Route::redirect('/', '/products');
