<?php

use App\Http\Controllers\API\CostCenter\CostCenterController;
use App\Http\Controllers\API\Customer\CustomerController;
use App\Http\Controllers\API\Department\DepartmentController;
use App\Http\Controllers\API\Discount\DiscountController;
use App\Http\Controllers\API\File\FileController;
use App\Http\Controllers\API\ItemCategory\ItemCategoryController;
use App\Http\Controllers\API\ItemProduct\ItemProductController;
use App\Http\Controllers\API\Location\LocationController;
use App\Http\Controllers\API\Param\ParamController;
use App\Http\Controllers\API\PriceList\PriceListController;
use App\Http\Controllers\API\PurchaseOrder\CateringPOController;
use App\Http\Controllers\API\PurchaseOrder\IncomingPOController;
use App\Http\Controllers\API\PurchaseRequest\PurchaseRequestController;
use App\Http\Controllers\API\Quotation\QuotationController;
use App\Http\Controllers\API\Supplier\SupplierController;
use App\Http\Controllers\API\User\Auth\AuthController;
use App\Http\Controllers\API\User\Auth\LoginController;
use App\Http\Controllers\API\User\Auth\LogoutController;
use App\Http\Controllers\API\User\Auth\PasswordResetController;
use App\Http\Controllers\API\User\PermissionController;
use App\Http\Controllers\API\User\UserController;
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
    Route::prefix('param')->group(function () {
        Route::get('unit', [ParamController::class, 'unit']);
    });

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
        Route::post('/import', [DepartmentController::class, 'import']);
        Route::get('/{department:id}', [DepartmentController::class, 'show']);
        Route::patch('/{department:id}', [DepartmentController::class, 'update']);
        Route::delete('/{department:id}', [DepartmentController::class, 'destroy']);
    });

    Route::prefix('location')->group(function () {
        Route::get('/', [LocationController::class, 'get']);
        Route::post('/', [LocationController::class, 'store']);
        Route::post('/import', [LocationController::class, 'import']);
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
        Route::post('/import', [SupplierController::class, 'import']);
        Route::get('/{supplier:id}', [SupplierController::class, 'show']);
        Route::patch('/{supplier:id}', [SupplierController::class, 'update']);
        Route::delete('/{supplier:id}', [SupplierController::class, 'destroy']);
    });

    Route::prefix('discount')->group(function () {
        Route::get('/', [DiscountController::class, 'get']);
        Route::post('/', [DiscountController::class, 'store']);
        Route::post('/import', [DiscountController::class, 'import']);
        Route::get('/{discount:id}', [DiscountController::class, 'show']);
        // Route::patch('/{discount:id}', [DiscountController::class, 'update']);
        Route::delete('/{discount:id}', [DiscountController::class, 'destroy']);
    });

    Route::prefix('customer')->group(function () {
        Route::get('/', [CustomerController::class, 'get']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::post('/import', [CustomerController::class, 'import']);
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

    Route::prefix('item-product')->group(function () {
        Route::get('/', [ItemProductController::class, 'get']);
        Route::post('/', [ItemProductController::class, 'store']);
        Route::get('/{item_product:id}', [ItemProductController::class, 'show']);
        Route::patch('/{item_product:id}', [ItemProductController::class, 'update']);
        Route::delete('/{item_product:id}', [ItemProductController::class, 'destroy']);
    });

    Route::prefix('price-list')->group(function () {
        Route::get('/', [PriceListController::class, 'get']);
        Route::post('/', [PriceListController::class, 'store']);
        Route::get('/{price_list:id}', [PriceListController::class, 'show']);
        Route::patch('/{price_list:id}', [PriceListController::class, 'update']);
        Route::delete('/{price_list:id}', [PriceListController::class, 'destroy']);
    });

    Route::prefix('file')->group(function() {
        Route::get('/', [FileController::class, 'get_file']);
        Route::get('/show', [FileController::class, 'show_file']);
        Route::post('/', [FileController::class, 'store']);
        Route::delete('/{file:id}', [FileController::class, 'destroy']);
    });
    
    Route::prefix('quotation')->group(function () {
        Route::get('/', [QuotationController::class, 'get']);
        Route::post('/', [QuotationController::class, 'store']);
        Route::get('/{quotation:id}', [QuotationController::class, 'show']);
        Route::patch('/{quotation:id}', [QuotationController::class, 'update']);
        Route::patch('/{quotation:id}/update-approval-status', [QuotationController::class, 'update_approval_status']);
        Route::patch('/{quotation:id}/update-status', [QuotationController::class, 'update_status']);
        Route::delete('/{quotation:id}', [QuotationController::class, 'destroy']);
    });

    Route::prefix('purchase-request')->group(function () {
        Route::get('/', [PurchaseRequestController::class, 'get']);
        Route::post('/', [PurchaseRequestController::class, 'store']);
        Route::get('/{purchase_request:id}', [PurchaseRequestController::class, 'show']);
        Route::patch('/{purchase_request:id}', [PurchaseRequestController::class, 'update']);
        Route::patch('/{purchase_request:id}/update-approval-status', [PurchaseRequestController::class, 'update_approval_status']);
        Route::patch('/{purchase_request:id}/update-status', [PurchaseRequestController::class, 'update_status']);
        Route::delete('/{purchase_request:id}', [PurchaseRequestController::class, 'destroy']);
    });

    Route::prefix('incoming-po')->group(function () {
        Route::get('/', [IncomingPOController::class, 'get']);
        Route::post('/', [IncomingPOController::class, 'store']);
        Route::get('/{incoming_po:id}', [IncomingPOController::class, 'show']);
        Route::patch('/{incoming_po:id}', [IncomingPOController::class, 'update']);
        Route::delete('/{incoming_po:id}', [IncomingPOController::class, 'destroy']);
    });

    Route::prefix('catering-po')->group(function () {
        Route::get('/', [CateringPOController::class, 'get']);
        Route::post('/', [CateringPOController::class, 'store']);
        Route::get('/{catering_po:id}', [CateringPOController::class, 'show']);
        Route::patch('/{catering_po:id}', [CateringPOController::class, 'update']);
        Route::patch('/{catering_po:id}/update-approval-status', [CateringPOController::class, 'update_approval_status']);
        Route::patch('/{catering_po:id}/update-status', [CateringPOController::class, 'update_status']);
        Route::delete('/{catering_po:id}', [CateringPOController::class, 'destroy']);
    });
});
