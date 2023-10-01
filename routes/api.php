<?php

use App\Http\Controllers\API\CostCenter\CostCenterController;
use App\Http\Controllers\API\Department\DepartmentController;
use App\Http\Controllers\API\Location\LocationController;
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
        Route::post('reset-password/without-confirmation', [PasswordResetController::class, 'without_confirmation']);
        Route::post('reset-password/with-old-password', [PasswordResetController::class, 'with_old_password']);
    });

    Route::prefix('department')->group(function () {
        Route::get('/', [DepartmentController::class, 'get']);
        Route::post('/', [DepartmentController::class, 'store']);
        Route::get('/{department:id}', [DepartmentController::class, 'show']);
        Route::patch('/{department:id}', [DepartmentController::class, 'update']);
        Route::delete('/{department:id}', [DepartmentController::class, 'destory']);
    });

    Route::prefix('location')->group(function () {
        Route::get('/', [LocationController::class, 'get']);
        Route::post('/', [LocationController::class, 'store']);
        Route::get('/{location:id}', [LocationController::class, 'show']);
        Route::patch('/{location:id}', [LocationController::class, 'update']);
        Route::delete('/{location:id}', [LocationController::class, 'destory']);
    });

    Route::prefix('cost-center')->group(function () {
        Route::get('/', [CostCenterController::class, 'get']);
        Route::post('/', [CostCenterController::class, 'store']);
        Route::get('/{cost_center:id}', [CostCenterController::class, 'show']);
        Route::patch('/{cost_center:id}', [CostCenterController::class, 'update']);
        Route::delete('/{cost_center:id}', [CostCenterController::class, 'destory']);
    });
});
