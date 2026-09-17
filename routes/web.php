<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
});

// Products
Route::get('/products/export', [ProductController::class, 'exportCsv'])->name('products.export');
Route::post('/products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('products.adjust-stock');
Route::resource('products', ProductController::class);

// Categories
Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

// Analytics / Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
