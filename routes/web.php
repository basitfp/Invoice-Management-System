<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Agent\InvoiceController as AgentInvoiceController;
use Illuminate\Support\Facades\Route;

// Welcome Page redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest-accessible auth routes (LoginController checks Auth::check() inside showLoginForm)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Authenticated auth routes
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin Protected Routes
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    Route::resource('categories', CategoryController::class, ['as' => 'admin']);
    Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])
         ->name('admin.categories.toggle-status');

    Route::resource('products', ProductController::class, ['as' => 'admin']);
    Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
         ->name('admin.products.toggle-status');

    // ✅ FIXED CUSTOMER ROUTES
    Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class, ['as' => 'admin']);
    Route::patch('customers/{customer}/toggle', [\App\Http\Controllers\Admin\CustomerController::class, 'toggle'])
         ->name('admin.customers.toggle');

    Route::resource('invoices', AdminInvoiceController::class, ['as' => 'admin']);
    Route::resource('settings', SettingsController::class, ['as' => 'admin']);
});

// Agent Protected Routes
Route::middleware(['agent'])->prefix('agent')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'agentDashboard'])->name('agent.dashboard');
    // Agent invoice routes (controllers to be implemented)
    Route::resource('invoices', AgentInvoiceController::class, ['as' => 'agent']);
});
