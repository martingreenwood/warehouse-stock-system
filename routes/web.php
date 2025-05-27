<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('orders')->group(
    function () {
        Route::get('/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/store', [OrderController::class, 'store'])->name('orders.store');
    }
);

Route::prefix('products')->group(
    function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
    }
);
