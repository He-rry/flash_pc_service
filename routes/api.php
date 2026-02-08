<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShopController as ApiShopController;
use App\Http\Controllers\Api\RouteController as ApiRouteController;
use App\Http\Controllers\Api\ActivityLogController as ApiLogController;
use App\Http\Controllers\ServiceController;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // --- 1. Public API (ဘယ်သူမဆို သုံးနိုင်သည်) ---
    Route::get('/track/{ticket_id}', [ServiceController::class, 'apiTrack']);

    // --- 2. Protected API (Auth လိုအပ်သည်) ---
    // Sanctum သည် Web Session ရော Token ကိုပါ လက်ခံသောကြောင့် ပိုမိုကောင်းမွန်သည်
    Route::middleware('auth:sanctum')->group(function () {

        // --- Shop Resources ---
        Route::middleware('permission:shop-view')->group(function () {
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
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});