<?php

use App\Http\Controllers\API\CostCenter\CostCenterController;
use App\Http\Controllers\API\Customer\CustomerController;
use App\Http\Controllers\API\Department\DepartmentController;
use App\Http\Controllers\API\Discount\DiscountController;
use App\Http\Controllers\API\Location\LocationController;
use App\Http\Controllers\API\Supplier\SupplierController;
use App\Http\Controllers\API\User\Auth\AuthController;
use App\Http\Controllers\API\User\Auth\LoginController;
use App\Http\Controllers\API\User\Auth\LogoutController;
use App\Http\Controllers\API\User\Auth\PasswordResetController;
use App\Http\Controllers\API\User\PermissionController;
use App\Http\Controllers\API\User\UserController;
use App\Http\Controllers\ItemCategory\ItemCategoryController;
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

        Route::prefix('role')->group(function () {
            Route::get('/', [PermissionController::class, 'get_role']);
            Route::post('/', [PermissionController::class, 'create_role']);
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
        Route::delete('/{department:id}', [DepartmentController::class, 'destroy']);
    });

    Route::prefix('location')->group(function () {
        Route::get('/', [LocationController::class, 'get']);
        Route::post('/', [LocationController::class, 'store']);
        Route::get('/{location:id}', [LocationController::class, 'show']);
        Route::patch('/{location:id}', [LocationController::class, 'update']);
        Route::delete('/{location:id}', [LocationController::class, 'destroy']);
    });

    Route::prefix('cost-center')->group(function () {
        Route::get('/', [CostCenterController::class, 'get']);
        Route::post('/', [CostCenterController::class, 'store']);
        Route::get('/{cost_center:id}', [CostCenterController::class, 'show']);
        Route::patch('/{cost_center:id}', [CostCenterController::class, 'update']);
        Route::delete('/{cost_center:id}', [CostCenterController::class, 'destroy']);
    });

    Route::prefix('supplier')->group(function () {
        Route::get('/', [SupplierController::class, 'get']);
        Route::post('/', [SupplierController::class, 'store']);
        Route::get('/{supplier:id}', [SupplierController::class, 'show']);
        Route::patch('/{supplier:id}', [SupplierController::class, 'update']);
        Route::delete('/{supplier:id}', [SupplierController::class, 'destroy']);
    });

    Route::prefix('discount')->group(function () {
        Route::get('/', [DiscountController::class, 'get']);
        Route::post('/', [DiscountController::class, 'store']);
        Route::get('/{discount:id}', [DiscountController::class, 'show']);
        Route::patch('/{discount:id}', [DiscountController::class, 'update']);
        Route::delete('/{discount:id}', [DiscountController::class, 'destroy']);
    });

    Route::prefix('customer')->group(function () {
        Route::get('/', [CustomerController::class, 'get']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::get('/{customer:id}', [CustomerController::class, 'show']);
        Route::patch('/{customer:id}', [CustomerController::class, 'update']);
        Route::delete('/{customer:id}', [CustomerController::class, 'destroy']);
    });

    Route::prefix('item-category')->group(function () {
        Route::get('/', [ItemCategoryController::class, 'get']);
        Route::post('/', [ItemCategoryController::class, 'store']);
        Route::get('/{item_category:id}', [ItemCategoryController::class, 'show']);
        Route::patch('/{item_category:id}', [ItemCategoryController::class, 'update']);
        Route::delete('/{item_category:id}', [ItemCategoryController::class, 'destroy']);
    });
});
