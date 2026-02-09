<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('customers')->name('customer.')->group(function () {
    Route::get('/report', function () {
        $service_types = \App\Models\ServiceType::all();
        return view('customers.report', compact('service_types'));
    })->name('report');

    Route::post('/report', [ServiceController::class, 'storeCustomerReport'])->name('report.submit');
    Route::get('/track', [ServiceController::class, 'track'])->name('track');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'AdminAuth'])->prefix('admin')->name('admin.')->group(function () {

    // --- 1. Dashboard ---
    Route::get('/', [ServiceController::class, 'index'])->name('dashboard');

    // --- 2. User Management (Merged logic with Soft Delete routes) ---
    Route::middleware(['permission:manage-users'])->group(function () {
        // Soft Delete & Restore routes (from 4af82d4)
        Route::post('users/{id}/restore', [UserController::class, 'restore'])
            ->name('users.restore');
        Route::post('users/{id}/force-delete', [UserController::class, 'forceDelete'])
            ->name('users.forceDelete');

        Route::resource('users', UserController::class);
        Route::get('/get-role-permissions/{name}', [UserController::class, 'getRolePermissions'])
            ->name('get_role_permissions');
    });

    // --- 3. Activity History (Logs) ---
    Route::middleware(['permission:view-logs'])->group(function () {
        Route::get('/activity-history', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('/shops/{id}/logs', [ShopController::class, 'getLogs'])->name('shops.logs');
    });

    // --- 4. Shops Management ---
    Route::get('shops/export', [ShopController::class, 'export'])
        ->middleware('permission:shop-export')
        ->name('shops.export');
    Route::post('shops/import', [ShopController::class, 'import'])
        ->middleware('permission:shop-import')
        ->name('shops.import');
    Route::get('shops/export-duplicates', [ShopController::class, 'downloadDuplicates'])
        ->middleware('permission:shop-export')
        ->name('shops.download.duplicates');

    Route::resource('shops', ShopController::class);

    // --- 5. Route Planner (Maps) ---
    Route::middleware('permission:route-view')->group(function () {
        Route::get('/saved_map_route', [RouteController::class, 'savedRoutes'])->name('maps.saved');
        Route::get('/show_route/{id}', [RouteController::class, 'showRoute'])->name('maps.show');
        Route::get('/route-planner', [RouteController::class, 'index'])->name('maps.index');
    });

    Route::post('/routes/store', [RouteController::class, 'store'])
        ->middleware('permission:route-create')
        ->name('maps.store');
    Route::delete('/routes/{id}', [RouteController::class, 'destroy'])
        ->middleware('permission:route-delete')
        ->name('maps.destroy');

    // --- 6. Services & Settings ---
    Route::resource('services', ServiceController::class)->middleware('permission:view-services');

    Route::middleware('permission:view-settings')->group(function () {
        Route::resource('statuses', StatusController::class);
        Route::resource('service-types', ServiceTypeController::class);
    });
});