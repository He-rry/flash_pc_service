<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShopController as ApiShopController;
use App\Http\Controllers\Api\RouteController as ApiRouteController;
use App\Http\Controllers\Api\ActivityLogController as ApiLogController;
use App\Http\Controllers\Api\StatusController as ApiStatusController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AuthController as ApiAuthController;
use App\Http\Controllers\Api\ServiceTypeController as ApiServiceTypeController;
use App\Http\Controllers\Api\ServiceController as ApiServiceController;
use App\Http\Controllers\Api\RoutePlannerController as ApiRoutePlannerController;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // --- Public API ---
    Route::post('/login', [ApiAuthController::class, 'apiLogin']);
    Route::get('/track/{ticket_id}', [ServiceController::class, 'apiTrack']);
    Route::post('/services/report', [ApiServiceController::class, 'storeCustomerReport']);
    Route::get('/services/track', [ApiServiceController::class, 'track']);

    // ---  Protected API ) ---
    // Sanctum 
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [ApiAuthController::class, 'apiLogout']);
        // --- Shop Resources ---
        Route::middleware('permission:view-shop-management')->group(function () {
            Route::get('/shops', [ApiShopController::class, 'index'])->name('api.shops.index');
            Route::get('/shops/{id}', [ApiShopController::class, 'show'])->name('api.shops.show');
        });

        Route::middleware(['permission:manage-shops'])->group(function () {
            Route::post('/shops', [ApiShopController::class, 'store']);
            Route::put('/shops/{id}', [ApiShopController::class, 'update']);
            Route::delete('/shops/{id}', [ApiShopController::class, 'destroy']);
        });

        // --- Route Planner (Maps) Resources ---
        Route::middleware('permission:route-view')->group(function () {
            Route::get('/routes', [ApiRouteController::class, 'index']);
            Route::get('/routes/{id}', [ApiRouteController::class, 'show']);
        });

        Route::middleware(['permission:manage-routes'])->group(function () {
            Route::post('/routes', [ApiRouteController::class, 'store']);
            Route::delete('/routes/{id}', [ApiRouteController::class, 'destroy']);
        });

        // --- Activity Logs API ---
        Route::middleware(['permission:view-logs'])->group(function () {
            Route::get('/activity-logs', [ApiLogController::class, 'index']);
            Route::get('/activity-logs/{id}', [ApiLogController::class, 'show']);
        });

        // --- Current User Profile ---
        Route::get('user', function (Request $request) {
            return $request->user();
        });
        //---Status API--
        Route::middleware(['permission:manage-services'])->group(function () {
            Route::apiResource('/statuses', ApiStatusController::class);
            Route::apiResource('service-types', ApiServiceTypeController::class);
        });
        //---Service API--
        Route::apiResource('services', ApiServiceController::class);
        //--Route API--
        Route::apiResource('routes', ApiRouteController::class);
        Route::get('route-planner', [ApiRoutePlannerController::class, 'index']);
        Route::get('/routes/{id}/show', [ApiRouteController::class, 'showRoute']);
    });
});
