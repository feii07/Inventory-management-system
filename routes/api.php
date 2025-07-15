<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::resource('users', UserController::class);
    
    Route::resource('roles', RoleController::class);
    
    Route::resource('items', ItemController::class);

    Route::apiResource('permissions', PermissionController::class);

    Route::apiResource('menus', MenuController::class);

    Route::get('/dashboard-stats', [DashboardController::class, 'stats']);

});

Route::options('/{any}', function () {
    return response()->json(['message' => 'OK'], 200);
})->where('any', '.*');