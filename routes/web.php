<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\signupController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Route::get('/about', function () {
    return view('about');
});

Route::get('/car/search', [CarController::class, 'search'])->name('car.search');

Route::middleware('auth')->group(function () {
    Route::resource('car', CarController::class)->only([
        'create', 'store', 'edit', 'update', 'destroy',
    ]);
});

Route::resource('car', CarController::class)->only(['index', 'show']);

Route::get('/signup', [signupController::class, 'index'])->name('signup');

Route::get('/login', [loginController::class, 'index'])->name('login');

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

