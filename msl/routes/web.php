<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $redisPing = Redis::connection()->ping();
        Cache::store('redis')->put('health_check', 'ok', 5);
        $cacheVal = Cache::store('redis')->get('health_check');

        return response()->json([
            'ok' => true,
            'services' => [
                'db' => 'ok',
                'redis' => $redisPing,
                'cache' => $cacheVal,
            ]
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});
