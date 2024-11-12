<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::prefix('/auth')->as('auth.')->group(function(){
    Route::get('/', [AuthController::class, 'auth'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/refresh', [AuthController::class, 'refreshToken'])->name('refresh');
});

Route::middleware('auth:api')->group(function() {
    Route::prefix('/auth')->as('auth.')->group(function(){
        Route::get('/validate', [AuthController::class, 'validate'])->name('validate');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
