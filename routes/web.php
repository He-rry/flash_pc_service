<?php

use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController; // ShopController ထည့်ဖို့မမေ့ပါနဲ့
use App\Http\Controllers\RouteController; // RouteController ထည့်ဖို့မမေ့ပါနဲ့
use Illuminate\Support\Facades\Route;

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

// --- Admin Routes (Name Prefix 'admin.' ကို ထည့်လိုက်ပါပြီ) ---
Route::middleware(['AdminAuth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [ServiceController::class, 'index'])->name('dashboard');

    // CRUD Resource Routes (အခုဆိုရင် admin.services.index စသဖြင့် ဖြစ်သွားပါပြီ)
    Route::resource('services', ServiceController::class);
    Route::resource('statuses', StatusController::class);
    Route::resource('service-types', ServiceTypeController::class);
    Route::resource('shops', ShopController::class);

    // Maps / Route Planner (Folder အသစ် လမ်းကြောင်းအတိုင်း နာမည်ပေးထားပါတယ်)
    Route::get('/route-planner', [RouteController::class, 'index'])->name('maps.index');
    Route::post('/routes/store', [RouteController::class, 'store'])->name('maps.store');
    Route::delete('/routes/{id}', [RouteController::class, 'destroy'])->name('maps.destroy');

    // routes/web.php
    Route::get('/saved_map_route', [RouteController::class, 'savedRoutes'])->name('maps.saved');
    Route::get('/show_route/{id}', [RouteController::class, 'showRoute'])->name('maps.show');

    // (လိုအပ်ရင်) Route ကို ပြန်ဖျက်ဖို့ Feature
    Route::delete('/saved_map_route/{id}', [RouteController::class, 'destroyRoute'])->name('maps.destroy');
});
