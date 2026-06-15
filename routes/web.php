<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/checkout', [CheckoutController::class, 'store']);
Route::get('/admin/orders/export', OrderExportController::class);
