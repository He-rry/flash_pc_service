<?php

use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController; // ShopController
use App\Http\Controllers\RouteController; // RouteController
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityLogController;

// --- Public Landing Page ---
Route::get('/', function () {
    return view('welcome');
})->name('home');

// --- Customer (Public) Routes ---
Route::prefix('customers')->name('customer.')->group(function () {
    Route::get('/report', function () {
        $service_types = \App\Models\ServiceType::all();
        return view('customers.report', compact('service_types'));
    })->name('report');
    Route::post('/report', [ServiceController::class, 'storeCustomerReport'])->name('report.submit');
    Route::get('/track', [ServiceController::class, 'track'])->name('track');
});
// --- Auth Routes ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::middleware(['AdminAuth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('dashboard');
    Route::resource('services', ServiceController::class);
    Route::resource('statuses', StatusController::class);
    Route::resource('service-types', ServiceTypeController::class);
    Route::get('shops/export', [ShopController::class, 'export'])->name('shops.export')->middleware('permission:manage-shops');
    Route::post('shops/import', [ShopController::class, 'import'])->name('shops.import')->middleware('permission:manage-shops');
    Route::get('shops/export-duplicates', [ShopController::class, 'downloadDuplicates'])
        ->name('shops.download.duplicates')->middleware('permission:manage-shops');
    Route::resource('shops', ShopController::class);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::get('/route-planner', [RouteController::class, 'index'])->name('maps.index');
    Route::post('/routes/store', [RouteController::class, 'store'])->name('maps.store')->middleware('permission:manage-routes');
    Route::delete('/routes/{id}', [RouteController::class, 'destroy'])->name('maps.destroy')->middleware('permission:manage-routes');
    // routes/web.php
    Route::get('/saved_map_route', [RouteController::class, 'savedRoutes'])->name('maps.saved');
    Route::get('/show_route/{id}', [RouteController::class, 'showRoute'])->name('maps.show');
    Route::get('/activity-history', [ActivityLogController::class, 'index'])->name('logs.index');
    Route::get('shops/{id}/logs', [App\Http\Controllers\ShopController::class, 'getLogs'])->name('admin.shops.logs');
});
