<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RouteController as ApiRouteController;
use App\Http\Controllers\Api\ShopController as ApiShopController;

// API v1: use web session so same-domain map page can auth; require login
Route::middleware(['web', 'auth'])->prefix('v1')->group(function () {
    // Route resources (read: any authenticated; write: manage-routes)
    Route::get('/routes', [ApiRouteController::class, 'index']);
    Route::get('/routes/{id}', [ApiRouteController::class, 'show']);
    Route::post('/routes', [ApiRouteController::class, 'store'])->middleware('permission:manage-routes');
    Route::delete('/routes/{id}', [ApiRouteController::class, 'destroy'])->middleware('permission:manage-routes');
    // Shop resources for map filtering/search (any authenticated admin can view)
    Route::get('/shops', [ApiShopController::class, 'index'])->name('shops.index');
});
