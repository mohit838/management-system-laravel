<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// API Test route
Route::get('/', fn() => response()->json(['message' => 'API is working']));

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes with JWT auth middleware
Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});
