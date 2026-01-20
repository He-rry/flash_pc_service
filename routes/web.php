<?php

use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
// routes/web.php ရဲ့ အပေါ်ဆုံးမှာ ဒါပါရပါမယ်
use App\Http\Controllers\ShopController;
use App\Http\Controllers\RouteController;

// Admin Group ထဲမှာ သေချာထည့်ပါ
Route::middleware(['AdminAuth'])->prefix('admin')->group(function () {

    Route::get('/', [ServiceController::class, 'index'])->name('admin.dashboard');
    Route::resource('services', ServiceController::class);
    Route::resource('statuses', StatusController::class);
    Route::resource('service-types', ServiceTypeController::class);

    // Shops အတွက် Resource Route
    // ဒါက admin.shops.index, admin.shops.create, admin.shops.store စတာတွေကို အလိုအလျောက် ဖန်တီးပေးပါတယ်
    Route::resource('shops', ShopController::class);

    Route::resource('routes', RouteController::class);
});
// --- Public Landing Page ---
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ---  Customer (Public) Routes ---
Route::prefix('customers')->name('customer.')->group(function () {
    // Report Issue Form
    Route::get('/report', function () {
        $service_types = \App\Models\ServiceType::all();
        return view('customers.report', compact('service_types'));
    })->name('report');

    // Submit Report
    Route::post('/report', [ServiceController::class, 'storeCustomerReport'])->name('report.submit');

    // Track Status
    Route::get('/track', [ServiceController::class, 'track'])->name('track');
});

// ---Auth Routes (Login/Logout) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// --Admin Routes (Protected by AdminAuth Middleware) ---
Route::middleware(['AdminAuth'])->prefix('admin')->group(function () {

    Route::get('/', [ServiceController::class, 'index'])->name('admin.dashboard');
    Route::resource('services', ServiceController::class);
    Route::resource('statuses', StatusController::class);
    Route::resource('service-types', ServiceTypeController::class);
});
Route::resource('routes', RouteController::class);
// Route Planner (မြေပုံပေါ်မှာ လမ်းကြောင်းဆွဲတဲ့ Page)
Route::get('/route-planner', [RouteController::class, 'index'])->name('routes.index');
Route::post('/routes/store', [RouteController::class, 'store'])->name('routes.create');
Route::resource('shops', ShopController::class);
Route::resource('routes', RouteController::class);
