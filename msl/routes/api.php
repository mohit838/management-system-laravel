<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::get('/role-test', function () {
    $user = auth()->user();

    // Create role + permission (only once)
    $role = Role::firstOrCreate(['name' => 'admin']);
    $permission = Permission::firstOrCreate(['name' => 'manage users']);

    // Assign role + permission to user
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    return response()->json([
        'user'        => $user->only(['id', 'name', 'email']),
        'roles'       => $user->getRoleNames(),
        'permissions' => $user->getAllPermissions()->pluck('name'),
    ]);
})->middleware('auth:api');



Route::get('/admin-only', function () {
    return response()->json(['message' => 'Welcome, admin 🚀']);
})->middleware(['auth:api', 'role:admin']);

Route::get('/manage-users', function () {
    return response()->json(['message' => 'You can manage users ✅']);
})->middleware(['auth:api', 'permission:manage users']);

Route::get('/admin-or-manager', function () {
    return response()->json(['message' => 'You are either Admin or Manager!']);
})->middleware(['auth:api', 'role_or_permission:admin|manager']);



Route::get('/test', function () {
    return response()->json([
        'ok' => true,
        'message' => 'API is working 🚀',
        'time' => now()->toDateTimeString(),
    ]);
});


Route::get('/health', function () {
    try {
        DB::select('select 1');                 // DB OK
        $pong = Redis::connection()->ping();    // Redis OK
        Cache::store('redis')->put('h', 'ok', 5); // Cache OK

        return response()->json([
            'ok'    => true,
            'db'    => 'ok',
            'redis' => $pong,
            'cache' => Cache::store('redis')->get('h'),
            'time'  => now()->toDateTimeString(),
        ]);
    } catch (\Throwable $e) {
        return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
    }
});
