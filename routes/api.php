<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;


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