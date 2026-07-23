<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/about', function () {
    return view('about');
});

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Route::get('/signup', [App\Http\Controllers\signupController::class, 'index']); 

Route::get('/login', [App\Http\Controllers\loginController::class, 'index']); 
