<?php

use App\Http\Controllers\API\Client\ClientController;
use App\Http\Controllers\API\Customer\CustomerController;
use App\Http\Controllers\API\DeliveryOrder\CateringDOController;
use App\Http\Controllers\API\DeliveryOrder\IncomingDOController;
use App\Http\Controllers\API\DeliveryOrder\OutgoingDOController;
use App\Http\Controllers\API\Department\DepartmentController;
use App\Http\Controllers\API\Discount\DiscountController;
use App\Http\Controllers\API\File\FileController;
use App\Http\Controllers\API\ItemCategory\ItemCategoryController;
use App\Http\Controllers\API\ItemProduct\ItemProductController;
use App\Http\Controllers\API\Location\LocationController;
use App\Http\Controllers\API\MealSheet\MealSheetDailyController;
use App\Http\Controllers\API\MealSheet\MealSheetDailyRecordController;
use App\Http\Controllers\API\MealSheet\MealSheetGroupController;
use App\Http\Controllers\API\MealSheet\MealSheetMonthlyController;
use App\Http\Controllers\API\Param\ParamController;
use App\Http\Controllers\API\PurchaseOrder\CateringPOController;
use App\Http\Controllers\API\PurchaseOrder\IncomingPOController;
use App\Http\Controllers\API\PurchaseOrder\OutgoingPOContoller;
use App\Http\Controllers\API\PurchaseRequest\PurchaseRequestController;
use App\Http\Controllers\API\Quotation\QuotationController;
use App\Http\Controllers\API\ReadExcel\ReadExcelController;
use App\Http\Controllers\API\Stock\MorController;
use App\Http\Controllers\API\Stock\ProductStockController;
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
        Route::post('/import', [ItemCategoryController::class, 'import']);
        Route::get('/{item_category:id}', [ItemCategoryController::class, 'show']);
        Route::patch('/{item_category:id}', [ItemCategoryController::class, 'update']);
        Route::delete('/{item_category:id}', [ItemCategoryController::class, 'destroy']);
    });

    Route::prefix('item-product')->group(function () {
        Route::get('/', [ItemProductController::class, 'get']);
        Route::post('/', [ItemProductController::class, 'store']);
        Route::post('/import', [ItemProductController::class, 'import']);
        Route::post('/import-category-product-price', [ItemProductController::class, 'import_category_product_price']);
        Route::get('/{item_product:id}', [ItemProductController::class, 'show']);
        Route::patch('/{item_product:id}', [ItemProductController::class, 'update']);
        Route::delete('/{item_product:id}', [ItemProductController::class, 'destroy']);
    });

    Route::prefix('file')->group(function() {
        Route::get('/', [FileController::class, 'get_file']);
        Route::get('/show', [FileController::class, 'show_file']);
        Route::post('/', [FileController::class, 'store']);
        Route::delete('/{file:id}', [FileController::class, 'destroy']);
    });
    
    Route::prefix('read-excel')->group(function() {
        Route::post('product-price', [ReadExcelController::class, 'product_price_excel']);
        Route::post('meal-sheet-record', [ReadExcelController::class, 'meal_sheet_record']);
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

    Route::prefix('outgoing-po')->group(function () {
        Route::get('/', [OutgoingPOContoller::class, 'get']);
        Route::post('/', [OutgoingPOContoller::class, 'store']);
        Route::get('/{outgoing_po:id}', [OutgoingPOContoller::class, 'show']);
        Route::patch('/{outgoing_po:id}', [OutgoingPOContoller::class, 'update']);
        Route::patch('/{outgoing_po:id}/update-approval-status', [OutgoingPOContoller::class, 'update_approval_status']);
        Route::patch('/{outgoing_po:id}/update-status', [OutgoingPOContoller::class, 'update_status']);
        Route::delete('/{outgoing_po:id}', [OutgoingPOContoller::class, 'destroy']);
    });

    Route::prefix('incoming-do')->group(function () {
        Route::get('/', [IncomingDOController::class, 'get']);
        Route::post('/', [IncomingDOController::class, 'store']);
        Route::get('/{incoming_do:id}', [IncomingDOController::class, 'show']);
        Route::patch('/{incoming_do:id}', [IncomingDOController::class, 'update']);
        Route::delete('/{incoming_do:id}', [IncomingDOController::class, 'destroy']);
    });

    Route::prefix('catering-do')->group(function () {
        Route::get('/', [CateringDOController::class, 'get']);
        Route::post('/', [CateringDOController::class, 'store']);
        Route::get('/{catering_do:id}', [CateringDOController::class, 'show']);
        Route::patch('/{catering_do:id}', [CateringDOController::class, 'update']);
        Route::patch('/{catering_do:id}/update-approval-status', [CateringDOController::class, 'update_approval_status']);
        Route::patch('/{catering_do:id}/update-status', [CateringDOController::class, 'update_status']);
        Route::delete('/{catering_do:id}', [CateringDOController::class, 'destroy']);
    });

    Route::prefix('outgoing-do')->group(function () {
        Route::get('/', [OutgoingDOController::class, 'get']);
        Route::post('/', [OutgoingDOController::class, 'store']);
        Route::get('/{outgoing_do:id}', [OutgoingDOController::class, 'show']);
        Route::patch('/{outgoing_do:id}', [OutgoingDOController::class, 'update']);
        Route::delete('/{outgoing_do:id}', [OutgoingDOController::class, 'destroy']);
    });

    Route::prefix('client')->group(function () {
        Route::get('/', [ClientController::class, 'get']);
        Route::post('/', [ClientController::class, 'store']);
        Route::post('/import', [ClientController::class, 'import']);
        Route::get('/{client:id}', [ClientController::class, 'show']);
        Route::patch('/{client:id}', [ClientController::class, 'update']);
        Route::delete('/{client:id}', [ClientController::class, 'destroy']);
    });

    Route::prefix('meal-sheet')->group(function () {

        Route::prefix('group')->group(function () {
            Route::get('/', [MealSheetGroupController::class, 'get']);
            Route::post('/', [MealSheetGroupController::class, 'store']);
            Route::get('/{meal_sheet_group:id}', [MealSheetGroupController::class, 'show']);
            Route::post('/{meal_sheet_group:id}', [MealSheetGroupController::class, 'update']);
            Route::delete('/{meal_sheet_group:id}', [MealSheetGroupController::class, 'destroy']);
        });

        Route::prefix('daily')->group(function () {
            Route::get('/', [MealSheetDailyController::class, 'get']);
            Route::post('/', [MealSheetDailyController::class, 'store']);
            Route::get('/{meal_sheet_daily:id}', [MealSheetDailyController::class, 'show']);
            Route::patch('/{meal_sheet_daily:id}', [MealSheetDailyController::class, 'update']);
            Route::delete('/{meal_sheet_daily:id}', [MealSheetDailyController::class, 'destroy']);
        });

        Route::prefix('daily-record')->group(function () {
            Route::get('/', [MealSheetDailyRecordController::class, 'get']);
            Route::post('/', [MealSheetDailyRecordController::class, 'store']);
            Route::get('/{meal_sheet_detail:id}', [MealSheetDailyRecordController::class, 'show']);
            Route::post('/{meal_sheet_detail:id}', [MealSheetDailyRecordController::class, 'update']);
            Route::delete('/{meal_sheet_detail:id}', [MealSheetDailyRecordController::class, 'destroy']);
            Route::post('/{meal_sheet_detail:id}/daily-meal-sheet-pdf', [MealSheetDailyRecordController::class, 'daily_meal_sheet_pdf']);
        });

        Route::prefix('monthly')->group(function () {
            Route::get('/', [MealSheetMonthlyController::class, 'get']);
            Route::post('/upsert', [MealSheetMonthlyController::class, 'upsert']);
            Route::get('/show-by-date', [MealSheetMonthlyController::class, 'show_by_date']);
            Route::get('/{meal_sheet_monthly:id}', [MealSheetMonthlyController::class, 'show']);
            Route::post('/{meal_sheet_monthly:id}/monthly-meal-sheet-pdf', [MealSheetMonthlyController::class, 'monthly_meal_sheet_pdf']);
            Route::delete('/{meal_sheet_monthly:id}', [MealSheetMonthlyController::class, 'destroy']);
        });
        
    });

    Route::prefix('product_stock')->group(function () {
        Route::get('/', [ProductStockController::class, 'get']);
        Route::post('upsert', [ProductStockController::class, 'upsert']);
        Route::get('/show', [ProductStockController::class, 'show']);
        Route::get('/{product_stock:id}/history', [ProductStockController::class, 'history']);
    });

    Route::prefix('mor')->group(function () {
        Route::post('/upsert', [MorController::class, 'upsert']);
    });
});
