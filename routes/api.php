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
        Route::post('/cars', [CarController::class, 'store'])->name('api.cars.store');
        Route::put('/cars/{car}', [CarController::class, 'update'])->name('api.cars.update');
        Route::delete('/cars/{car}', [CarController::class, 'destroy'])->name('api.cars.destroy');
        Route::patch('/cars/{car}/restore', [CarController::class, 'restore'])
            ->withTrashed()
            ->name('api.cars.restore');
        Route::post('/cars/{car}/favourite', [CarController::class, 'favourite'])
            ->name('api.cars.favourite');
        Route::get('/favourites', [CarController::class, 'favourites'])
            ->name('api.favourites');
    });

    Route::get('/cars', [CarController::class, 'index'])
        ->middleware('throttle:60,1')
        ->name('api.cars.index');

    Route::get('/cars/{car}', [CarController::class, 'show'])->name('api.cars.show');
});
