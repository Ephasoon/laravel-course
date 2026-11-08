<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('api.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
        Route::get('/user', [AuthController::class, 'user'])->name('api.user');
        Route::get('/my-cars', [CarController::class, 'myCars'])->name('api.my-cars');
    });

    Route::get('/cars', [CarController::class, 'index'])
        ->middleware('throttle:60,1')
        ->name('api.cars.index');

    Route::get('/cars/{car}', [CarController::class, 'show'])->name('api.cars.show');
});
