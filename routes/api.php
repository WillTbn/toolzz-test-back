<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatMessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::prefix('/auth')->as('auth.')->group(function(){
    Route::post('/', [AuthController::class, 'auth'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/refresh', [AuthController::class, 'refreshToken'])->name('refresh');
});

Route::middleware('auth:api')->group(function() {
    Route::prefix('/auth')->as('auth.')->group(function(){
        Route::get('/validate', [AuthController::class, 'validate'])->name('validate');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
    Route::controller(ChatController::class)
    ->prefix('/chat')
    ->as('chat.')
    ->group(function(){
        Route::get('/', 'getAll')->name('getAll');
        Route::post('/', 'createChat')->name('create');
    });
    //chatMessage
    Route::controller(ChatMessageController::class)
        ->prefix('/chat-message')
        ->as('chat.message.')
        ->group(function(){
            Route::post('/{chat:hash_id}', 'store')->name('store');
            Route::get('/{chat:hash_id}', 'getAllChatMessage')->name('getAllChatMessage');
    });
});
