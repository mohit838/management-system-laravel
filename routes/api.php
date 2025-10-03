<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

// Public routes
Route::get('/', [ApiController::class, 'index']);
Route::post('/register', [ApiController::class, 'register']);
Route::post('/login', [ApiController::class, 'login']);

// Protected routes with JWT auth middleware
Route::middleware('auth:api')->group(function () {
    Route::get('/me', [ApiController::class, 'me']);
    Route::post('/logout', [ApiController::class, 'logout']);
    Route::post('/refresh', [ApiController::class, 'refresh']);
});
