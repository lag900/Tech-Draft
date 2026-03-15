<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);
Route::get('/login', function () {
    return view('welcome');
})->name('login');



Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
