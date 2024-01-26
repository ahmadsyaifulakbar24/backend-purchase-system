<?php

use App\Http\Controllers\API\ActivityLog\ActivityLogController;
use App\Http\Controllers\API\Client\ClientController;
use App\Http\Controllers\API\Customer\CustomerController;
use App\Http\Controllers\API\Department\DepartmentController;
use App\Http\Controllers\API\Discount\DiscountController;
use App\Http\Controllers\API\ExternalOrder\DOCustomerController;
use App\Http\Controllers\API\ExternalOrder\POCustomerController;
use App\Http\Controllers\API\ExternalOrder\POSupplierCustomerController;
use App\Http\Controllers\API\ExternalOrder\PRCustomerController;
use App\Http\Controllers\API\ExternalOrder\QuotationController;
use App\Http\Controllers\API\File\FileController;
use App\Http\Controllers\API\InternalOrder\DOCateringController;
use App\Http\Controllers\API\InternalOrder\POCateringController;
use App\Http\Controllers\API\InternalOrder\POSupplierCateringController;
use App\Http\Controllers\API\InternalOrder\PRCateringController;
use App\Http\Controllers\API\ItemCategory\ItemCategoryController;
use App\Http\Controllers\API\ItemProduct\ItemProductController;
use App\Http\Controllers\API\Location\LocationController;
use App\Http\Controllers\API\MealSheet\MealSheetDailyController;
use App\Http\Controllers\API\MealSheet\MealSheetDailyRecordController;
use App\Http\Controllers\API\MealSheet\MealSheetGroupController;
use App\Http\Controllers\API\MealSheet\MealSheetMonthlyController;
use App\Http\Controllers\API\OrderHistory\OrderHistoryController;
use App\Http\Controllers\API\Param\ParamController;
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

    Route::prefix('client')->group(function () {
        Route::get('/', [ClientController::class, 'get']);
        Route::post('/', [ClientController::class, 'store']);
        Route::post('/import', [ClientController::class, 'import']);
        Route::get('/{client:id}', [ClientController::class, 'show']);
        Route::patch('/{client:id}', [ClientController::class, 'update']);
        Route::delete('/{client:id}', [ClientController::class, 'destroy']);
    });


    // purchasing menu
        Route::prefix('pr-catering')->group(function () {
            Route::get('/', [PRCateringController::class, 'get']);
            Route::post('/', [PRCateringController::class, 'store']);
            Route::get('/{pr_catering:id}', [PRCateringController::class, 'show']);
            Route::patch('/{pr_catering:id}', [PRCateringController::class, 'update']);
            Route::delete('/{pr_catering:id}', [PRCateringController::class, 'destroy']);
        });

        Route::prefix('po-catering')->group(function () {
            Route::get('/', [POCateringController::class, 'get']);
            Route::post('/', [POCateringController::class, 'store']);
            Route::get('/{po_catering:id}', [POCateringController::class, 'show']);
            Route::patch('/{po_catering:id}', [POCateringController::class, 'update']);
            Route::patch('/{po_catering:id}/update-status', [POCateringController::class, 'update_status']);
            Route::patch('/{po_catering:id}/update-approval-status', [POCateringController::class, 'update_approval_status']);
            Route::delete('/{po_catering:id}', [POCateringController::class, 'destroy']);
        });

        Route::prefix('po-supplier-catering')->group(function () {
            Route::get('/', [POSupplierCateringController::class, 'get']);
            Route::post('/', [POSupplierCateringController::class, 'store']);
            Route::get('/{po_supplier_catering:id}', [POSupplierCateringController::class, 'show']);
            Route::patch('/{po_supplier_catering:id}', [POSupplierCateringController::class, 'update']);
            Route::patch('/{po_supplier_catering:id}/update-status', [POSupplierCateringController::class, 'update_status']);
            Route::delete('/{po_supplier_catering:id}', [POSupplierCateringController::class, 'destroy']);
        });

        Route::prefix('do-catering')->group(function () {
            Route::get('/', [DOCateringController::class, 'get']);
            Route::get('/{do_catering:id}', [DOCateringController::class, 'show']);
            Route::patch('/{do_catering:id}/update-status', [DOCateringController::class, 'update_status']);
            Route::patch('/{do_catering:id}', [DOCateringController::class, 'update']);
        });

        Route::prefix('pr-customer')->group(function () {
            Route::get('/', [PRCustomerController::class, 'get']);
            Route::post('/', [PRCustomerController::class, 'store']);
            Route::get('/{pr_customer:id}', [PRCustomerController::class, 'show']);
            Route::patch('/{pr_customer:id}', [PRCustomerController::class, 'update']);
            Route::delete('/{pr_customer:id}', [PRCustomerController::class, 'destroy']);
        });

        Route::prefix('quotation')->group(function () {
            Route::get('/', [QuotationController::class, 'get']);
            Route::post('/', [QuotationController::class, 'store']);
            Route::get('/{quotation:id}', [QuotationController::class, 'show']);
            Route::patch('/{quotation:id}', [QuotationController::class, 'update']);
            Route::patch('/{quotation:id}/update-status', [QuotationController::class, 'update_status']);
            Route::patch('/{quotation:id}/update-approval-status', [QuotationController::class, 'update_approval_status']);
            Route::delete('/{quotation:id}', [QuotationController::class, 'destroy']);
        });

        Route::prefix('po-customer')->group(function () {
            Route::get('/', [POCustomerController::class, 'get']);
            Route::post('/', [POCustomerController::class, 'store']);
            Route::get('/{po_customer:id}', [POCustomerController::class, 'show']);
            Route::patch('/{po_customer:id}', [POCustomerController::class, 'update']);
            Route::patch('/{po_customer:id}/update-status', [POCustomerController::class, 'update_status']);
            Route::patch('/{po_customer:id}/update-approval-status', [POCustomerController::class, 'update_approval_status']);
            Route::delete('/{po_customer:id}', [POCustomerController::class, 'destroy']);
        });

        Route::prefix('po-supplier-customer')->group(function () {
            Route::get('/', [POSupplierCustomerController::class, 'get']);
            Route::post('/', [POSupplierCustomerController::class, 'store']);
            Route::get('/{po_supplier_customer:id}', [POSupplierCustomerController::class, 'show']);
            Route::patch('/{po_supplier_customer:id}', [POSupplierCustomerController::class, 'update']);
            Route::patch('/{po_supplier_customer:id}/update-status', [POSupplierCustomerController::class, 'update_status']);
            Route::delete('/{po_supplier_customer:id}', [POSupplierCustomerController::class, 'destroy']);
        });

        Route::prefix('do-customer')->group(function () {
            Route::get('/', [DOCustomerController::class, 'get']);
            Route::post('/', [DOCustomerController::class, 'store']);
            Route::get('/{do_customer:id}', [DOCustomerController::class, 'show']);
            Route::patch('/{do_customer:id}', [DOCustomerController::class, 'update']);
            Route::patch('/{do_customer:id}/update-status', [DOCustomerController::class, 'update_status']);
            Route::patch('/{do_customer:id}/update-approval-status', [DOCustomerController::class, 'update_approval_status']);
            Route::delete('/{do_customer:id}', [DOCustomerController::class, 'destroy']);
        });

    // end purchasing menu

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
        Route::get('/daily', [MorController::class, 'daily']);
        Route::get('/', [MorController::class, 'get']);
        Route::post('/upsert', [MorController::class, 'upsert']);
        // Route::get('/export', [MorController::class, 'export']);
    });

    Route::prefix('activity-log')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index']);
    });

    Route::prefix('order-history')->group(function () {
        Route::get('/', [OrderHistoryController::class, 'get']);
        Route::get('/{order_history:id}', [OrderHistoryController::class, 'show']);
    });

});

Route::get('mor/export', [MorController::class, 'export']);
