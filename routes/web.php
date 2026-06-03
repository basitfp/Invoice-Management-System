<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\SettingsController;

use App\Http\Controllers\Agent\InvoiceController as AgentInvoiceController;

// Welcome Page
Route::redirect('/', '/login');

// Authentication Routes
Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware('admin')->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])
        ->name('admin.dashboard');

    // Categories
    Route::get('/categories',                         [CategoryController::class, 'index'])  ->name('admin.categories.index');
    Route::get('/categories/create',                  [CategoryController::class, 'create']) ->name('admin.categories.create');
    Route::post('/categories',                        [CategoryController::class, 'store'])  ->name('admin.categories.store');
    Route::get('/categories/{category}',              [CategoryController::class, 'show'])   ->name('admin.categories.show');
    Route::get('/categories/{category}/edit',         [CategoryController::class, 'edit'])   ->name('admin.categories.edit');
    Route::put('/categories/{category}',              [CategoryController::class, 'update']) ->name('admin.categories.update');
    Route::delete('/categories/{category}',           [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::patch('/categories/{category}/toggle-status',[CategoryController::class,'toggleStatus'])->name('admin.categories.toggle-status');

    // Products
    Route::get('/products',                           [ProductController::class, 'index'])  ->name('admin.products.index');
    Route::get('/products/create',                    [ProductController::class, 'create']) ->name('admin.products.create');
    Route::post('/products',                          [ProductController::class, 'store'])  ->name('admin.products.store');
    Route::get('/products/{product}',                 [ProductController::class, 'show'])   ->name('admin.products.show');
    Route::get('/products/{product}/edit',            [ProductController::class, 'edit'])   ->name('admin.products.edit');
    Route::put('/products/{product}',                 [ProductController::class, 'update']) ->name('admin.products.update');
    Route::delete('/products/{product}',              [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::patch('/products/{product}/toggle-status', [ProductController::class,'toggleStatus'])->name('admin.products.toggle-status');

    // Customers
    Route::get('/customers',                          [CustomerController::class, 'index'])  ->name('admin.customers.index');
    Route::get('/customers/create',                   [CustomerController::class, 'create']) ->name('admin.customers.create');
    Route::post('/customers',                         [CustomerController::class, 'store'])  ->name('admin.customers.store');
    Route::get('/customers/{customer}',               [CustomerController::class, 'show'])   ->name('admin.customers.show');
    Route::get('/customers/{customer}/edit',          [CustomerController::class, 'edit'])   ->name('admin.customers.edit');
    Route::put('/customers/{customer}',               [CustomerController::class, 'update']) ->name('admin.customers.update');
    Route::delete('/customers/{customer}',            [CustomerController::class, 'destroy'])->name('admin.customers.destroy');
    Route::patch('/customers/{customer}/toggle',      [CustomerController::class, 'toggle']) ->name('admin.customers.toggle');

    // Admin Invoices
    Route::get('/invoices',                           [AdminInvoiceController::class, 'index'])       ->name('admin.invoices.index');
    Route::get('/invoices/create',                    [AdminInvoiceController::class, 'create'])      ->name('admin.invoices.create');
    Route::post('/invoices',                          [AdminInvoiceController::class, 'store'])       ->name('admin.invoices.store');
    Route::get('/invoices/{invoice}',                 [AdminInvoiceController::class, 'show'])        ->name('admin.invoices.show');
    Route::patch('/invoices/{invoice}/status',        [AdminInvoiceController::class, 'toggleStatus'])->name('admin.invoices.toggle-status');
    Route::delete('/invoices/{invoice}',              [AdminInvoiceController::class, 'destroy'])     ->name('admin.invoices.destroy');

    // ── Settings (single-row: index + update only) ──────────────────────
    Route::get('/settings',  [SettingsController::class, 'index']) ->name('admin.settings.index');
    Route::put('/settings',  [SettingsController::class, 'update'])->name('admin.settings.update');
});

/*
|--------------------------------------------------------------------------
| Agent Routes
|--------------------------------------------------------------------------
*/
Route::middleware('agent')->prefix('agent')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'agentDashboard'])
        ->name('agent.dashboard');

    // Agent Invoices
    Route::get('/invoices',                   [AgentInvoiceController::class, 'index'])  ->name('agent.invoices.index');
    Route::get('/invoices/create',            [AgentInvoiceController::class, 'create']) ->name('agent.invoices.create');
    Route::post('/invoices',                  [AgentInvoiceController::class, 'store'])  ->name('agent.invoices.store');
    Route::get('/invoices/{invoice}',         [AgentInvoiceController::class, 'show'])   ->name('agent.invoices.show');
    Route::get('/invoices/{invoice}/edit',    [AgentInvoiceController::class, 'edit'])   ->name('agent.invoices.edit');
    Route::put('/invoices/{invoice}',         [AgentInvoiceController::class, 'update']) ->name('agent.invoices.update');
    Route::delete('/invoices/{invoice}',      [AgentInvoiceController::class, 'destroy'])->name('agent.invoices.destroy');
});