<?php

use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuth;

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
    // CRUD Resource Routes
    Route::resource('services', ServiceController::class);
    Route::resource('statuses', StatusController::class);
    Route::resource('service-types', ServiceTypeController::class);
});
