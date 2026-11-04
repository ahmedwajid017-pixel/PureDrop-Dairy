<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum', 'admin'])->get('/admin/dashboard', function (Request $request) {
    return response()->json([
        'message' => 'Welcome to the admin dashboard.',
        'user' => $request->user()->name,
    ]);
});
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/deliveries/{delivery}/proof', [
        DeliveryController::class,
        'uploadProof',
    ]);
});
