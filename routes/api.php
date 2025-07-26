<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'api.'], function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::group(['prefix' => 'books', 'as' => 'books.'], function () {
        Route::get('/', [BookController::class, 'pagination'])->name('pagination');

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::post('/', [BookController::class, 'create'])->name('create')->middleware('api.onlyAdmin');
            Route::post('/{id}/lend', [BookController::class, 'lend'])->name('lend');
            Route::post('/{id}/return', [BookController::class, 'return'])->name('return');
        });
    });

    Route::group([
        'prefix' => 'users',
        'middleware' => [
            'auth:sanctum',
            'api.onlyAdmin',
        ],
        'as' => 'users.',
    ], function () {
        Route::get('/', [UserController::class, 'pagination'])->name('pagination');
        Route::post('/{id}/make-admin', [UserController::class, 'makeAdmin'])->name('makeAdmin');
    });
});
