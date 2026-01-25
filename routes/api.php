<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RouteController as ApiRouteController;
use App\Http\Controllers\Api\ShopController as ApiShopController;

Route::prefix('v1')->group(function () {
    // Route resources
    Route::get('/routes', [ApiRouteController::class, 'index']);
    Route::get('/routes/{id}', [ApiRouteController::class, 'show']);
    Route::post('/routes', [ApiRouteController::class, 'store']);
    Route::delete('/routes/{id}', [ApiRouteController::class, 'destroy']);
    // Shop resources for map filtering/search
    Route::get('/shops', [ApiShopController::class, 'index'])->name('shops.index');
});
