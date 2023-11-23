<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [RegisterController::class, 'store']);
Route::post('/auth/login', [LoginController::class, 'store']);

Route::prefix('news')->group(function() {
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/filter-options', [NewsController::class, 'filters']);
});

Route::middleware(['auth:sanctum'])->group(function() {

    Route::prefix('account')->group(function() {
        Route::get('/me', [AccountController::class, 'user']);
        Route::patch('/preference', [AccountController::class, 'updatePreference']);
    });

    Route::post('/auth/logout', [LoginController::class, 'destroy']);

    Route::get('/news/personalized', [NewsController::class, 'personalized']);
});
