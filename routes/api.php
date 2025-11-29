<?php

use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;

Route::middleware(['enable.cors', 'throttle:60,1'])->group(function () {
    // authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // admin route
        Route::middleware(['role:' . RoleEnum::ADMIN->value])->prefix('admin')->group(function () {
            Route::apiResource('movies', MovieController::class);
        });
        
        // staff route
        Route::middleware(['role:' . RoleEnum::STAFF->value])->prefix('staff')->group(function () {
            Route::apiResource('movies', MovieController::class)->only(['index', 'show', 'update']);
        });
        
        // user route
        Route::middleware(['role:' . RoleEnum::USER->value])->prefix('user')->group(function () {
            Route::get('movies', [MovieController::class, 'index']);
            Route::get('detail/{slug}', [MovieController::class, 'slug']);
        });
    });
});
