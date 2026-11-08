<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\FavouriteCarController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SignupController;
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

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/signup', [SignupController::class, 'index'])->name('signup');
    Route::post('/signup', [SignupController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->get('/watchlist', [FavouriteCarController::class, 'index'])->name('watchlist');

Route::middleware('auth')->post('/favourite/{car}', [FavouriteCarController::class, 'toggle'])->name('favourite.toggle');

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
