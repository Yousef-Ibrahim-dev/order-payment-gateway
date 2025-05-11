<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('refresh',  [AuthController::class, 'refresh']);
    Route::middleware('auth:api')->group(function() {
        Route::get('me',    [AuthController::class, 'me']);
        Route::post('logout',[AuthController::class, 'logout']);
    });
});



// Product routes
Route::get('products',           [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);




 // Order routes
Route::middleware(['auth:api'])->group(function () {
   // index, store, show, update, destroy
    Route::apiResource('orders', OrderController::class)
        ->parameters(['orders' => 'order']);
});


// Payment Gateway routes
/*Route::middleware(['auth:api'])->group(function() {
    Route::get('payment-gateways',          [PaymentGatewayController::class,'index']);
    Route::get('payment-gateways/{code}',   [PaymentGatewayController::class,'show']);
});*/



// Payment routes
Route::middleware(['auth:api'])->group(function() {
    Route::post('orders/{order}/payments', [PaymentController::class,'store']);
    Route::get('orders/{order}/payments', [PaymentController::class,'forOrder']);
    Route::get('payments',                 [PaymentController::class,'index']);
});
