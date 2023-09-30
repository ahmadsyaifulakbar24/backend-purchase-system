<?php

use App\Http\Controllers\API\User\Auth\AuthController;
use App\Http\Controllers\API\User\Auth\LoginController;
use App\Http\Controllers\API\User\Auth\LogoutController;
use App\Http\Controllers\API\User\Auth\PasswordResetController;
use App\Http\Controllers\API\User\Auth\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('/login', LoginController::class);
    
            Route::post('/forgot-password', [PasswordResetController::class, 'password_email']);
            Route::post('/reset-password', [PasswordResetController::class, 'password_update']);
        });
    });
});

Route::middleware(['auth:api'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::get('/', AuthController::class);
            Route::delete('/logout', LogoutController::class);
        });

        Route::get('/', [UserController::class, 'get']);
        Route::get('/{user:id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::post('/{user:id}', [UserController::class, 'update']);
        Route::post('{user:id}/update-photo', [UserController::class, 'update_photo']);
        Route::post('reset-password/without-confirmation', [UserController::class, 'without_confirmation']);
        Route::post('reset-password/with-old-password', [UserController::class, 'with_old_password']);
    });
});
