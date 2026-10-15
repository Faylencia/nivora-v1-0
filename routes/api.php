<?php

use App\Http\Controllers\Api\AiRecommendationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BusinessApplicationController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\CashierController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\MemberDirectoryController;
use App\Http\Controllers\Api\OutletController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\StoreAnalysisController;
use App\Http\Controllers\Api\SuperAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::prefix('auth')->middleware('throttle:auth')->group(function (): void {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::middleware('auth.token')->group(function (): void {
        Route::prefix('auth')->group(function (): void {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });

        Route::middleware('role:owner')->group(function (): void {
            Route::get('business', [BusinessController::class, 'show']);
            Route::put('business', [BusinessController::class, 'update']);
            Route::apiResource('outlets', OutletController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::apiResource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::apiResource('products', ProductController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::get('payments', [PaymentController::class, 'index']);
            Route::get('cashiers', [CashierController::class, 'index']);
            Route::post('cashiers', [CashierController::class, 'store']);
            Route::patch('cashiers/{cashier}', [CashierController::class, 'update']);
            Route::delete('cashiers/{cashier}', [CashierController::class, 'destroy']);
            Route::post('cashiers/{cashier}/points', [CashierController::class, 'fundPoints']);
            Route::apiResource('promotions', PromoController::class)
                ->only(['store', 'update', 'destroy'])
                ->parameters(['promotions' => 'promo']);
            Route::post('inventory/adjustment', [InventoryController::class, 'adjustment']);
            Route::get('inventory/movements', [InventoryController::class, 'movements']);
            Route::get('inventory', [InventoryController::class, 'index']);
            Route::get('customers', [CustomerController::class, 'index']);
            Route::get('customers/{customer}', [CustomerController::class, 'show']);
            Route::post('sales/{sale}/return', [SaleController::class, 'return']);
            Route::post('ai/recommendations/generate', [AiRecommendationController::class, 'generate']);
            Route::post('ai/recommendations/{recommendation}/execute', [AiRecommendationController::class, 'execute']);
            Route::get('ai/recommendations', [AiRecommendationController::class, 'index']);
            Route::post('ai/store-analysis', [StoreAnalysisController::class, 'store']);
            Route::get('ai/store-analysis/{analysis}', [StoreAnalysisController::class, 'show']);
        });

        Route::middleware('role:owner,cashier')->group(function (): void {
            Route::get('products', [ProductController::class, 'index']);
            Route::get('promotions', [PromoController::class, 'index']);
            Route::get('promotions/{promo}', [PromoController::class, 'show']);
            Route::get('products/{product}', [ProductController::class, 'show']);
            Route::get('outlets', [OutletController::class, 'index']);
            Route::get('outlets/{outlet}', [OutletController::class, 'show']);
            Route::get('categories', [CategoryController::class, 'index']);
            Route::get('inventory', [InventoryController::class, 'index']);
            Route::get('cashiers/member-lookup', [CashierController::class, 'lookupMember']);
            Route::post('sales', [SaleController::class, 'store']);
            Route::get('sales', [SaleController::class, 'index']);
            Route::get('sales/{sale}', [SaleController::class, 'show']);
            Route::get('dashboard', [DashboardController::class, 'index']);
            Route::get('dashboard/sales-chart', [DashboardController::class, 'salesChart']);
        });

        Route::middleware('role:owner')->group(function (): void {
            Route::post('customers', [CustomerController::class, 'store']);
            Route::put('customers/{customer}', [CustomerController::class, 'update']);
        });

        Route::middleware('role:member')->group(function (): void {
            Route::get('directory/businesses', [MemberDirectoryController::class, 'index']);
            Route::get('directory/businesses/{business}', [MemberDirectoryController::class, 'show']);
            Route::get('directory/products/{product}', [MemberDirectoryController::class, 'showProduct']);
            Route::post('business-applications', [BusinessApplicationController::class, 'store']);
            Route::get('member/business-applications', [BusinessApplicationController::class, 'myApplications']);
            Route::get('member/dashboard', [MemberController::class, 'dashboard']);
            Route::get('member/sales', [MemberController::class, 'sales']);
            Route::get('member/coupons', [MemberController::class, 'coupons']);
            Route::get('member/wallet', [MemberController::class, 'wallet']);
            Route::get('member/notifications', [MemberController::class, 'notifications']);
            Route::patch('member/notifications/{notification}/read', [MemberController::class, 'markNotificationRead']);
        });

        Route::middleware('role:super_admin')->prefix('super-admin')->group(function (): void {
            Route::get('business-options', [BusinessApplicationController::class, 'businessOptions']);
            Route::get('accounts', [SuperAdminController::class, 'accounts']);
            Route::post('accounts', [SuperAdminController::class, 'storeAccount']);
            Route::get('members', [SuperAdminController::class, 'members']);
            Route::delete('users/{user}', [SuperAdminController::class, 'destroyUser']);
            Route::patch('users/{user}', [SuperAdminController::class, 'updateUser']);
            Route::patch('businesses/{business}', [SuperAdminController::class, 'updateBusiness']);
            Route::post('businesses', [SuperAdminController::class, 'storeBusiness']);
            Route::delete('businesses/{business}', [SuperAdminController::class, 'destroyBusiness']);
            Route::post('categories', [SuperAdminController::class, 'storeCategory']);
            Route::patch('categories/{category}', [SuperAdminController::class, 'updateCategory']);
            Route::delete('categories/{category}', [SuperAdminController::class, 'destroyCategory']);
            Route::get('business-applications', [BusinessApplicationController::class, 'index']);
            Route::post('business-applications/{application}/approve', [BusinessApplicationController::class, 'approve']);
            Route::post('business-applications/{application}/reject', [BusinessApplicationController::class, 'reject']);
        });
    });
});
