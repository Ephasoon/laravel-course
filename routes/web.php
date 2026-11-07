<?php

use App\Http\Controllers\CarController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/about', function () {
    return view('about');
});

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Route::get('/car/search', [CarController::class, 'search'])->name('car.search');
Route::resource('car', CarController::class);

Route::get('/signup', [App\Http\Controllers\signupController::class, 'index'])->name(name:'signup'); 

Route::get('/login', [App\Http\Controllers\loginController::class, 'index'])->name(name:'login'); 
