<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::Post('/register', [ApiController::class, 'register']);
Route::Post('/login', [ApiController::class, 'login']);
Route::Get('/', [ApiController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    Route::Get('/me', [ApiController::class, 'me']);
    Route::Post('/logout', [ApiController::class, 'logout']);
});
