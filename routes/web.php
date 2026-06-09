<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\ManufacturerController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Agent\AgentDashboardController;
use App\Http\Controllers\Agent\AgentInvoiceController;

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
    Route::post('/categories/{category}/enable',      [CategoryController::class, 'enable']) ->name('admin.categories.enable');
    Route::patch('/categories/{category}/toggle-status',[CategoryController::class,'toggleStatus'])->name('admin.categories.toggle-status');

    // Areas
    Route::get('/areas',                              [AreaController::class, 'index'])       ->name('admin.areas.index');
    Route::post('/areas',                             [AreaController::class, 'store'])       ->name('admin.areas.store');
    Route::put('/areas/{area}',                       [AreaController::class, 'update'])      ->name('admin.areas.update');
    Route::delete('/areas/{area}',                    [AreaController::class, 'destroy'])     ->name('admin.areas.destroy');
    Route::patch('/areas/{area}/toggle-status',       [AreaController::class, 'toggleStatus'])->name('admin.areas.toggle-status');

    // Manufacturers
    Route::get('/manufacturers',                              [ManufacturerController::class, 'index'])       ->name('admin.manufacturers.index');
    Route::post('/manufacturers',                             [ManufacturerController::class, 'store'])       ->name('admin.manufacturers.store');
    Route::put('/manufacturers/{manufacturer}',               [ManufacturerController::class, 'update'])      ->name('admin.manufacturers.update');
    Route::delete('/manufacturers/{manufacturer}',            [ManufacturerController::class, 'destroy'])     ->name('admin.manufacturers.destroy');
    Route::patch('/manufacturers/{manufacturer}/toggle-status',[ManufacturerController::class,'toggleStatus'])->name('admin.manufacturers.toggle-status');
    
    // Vendors
    Route::get('/vendors',                            [VendorController::class, 'index'])->name('admin.vendors.index');
    Route::post('/vendors',                           [VendorController::class, 'store'])->name('admin.vendors.store');
    Route::put('/vendors/{vendor}',                   [VendorController::class, 'update'])->name('admin.vendors.update');
    Route::patch('/vendors/{vendor}/toggle-status',   [VendorController::class, 'toggleStatus'])->name('admin.vendors.toggle-status');
    Route::delete('/vendors/{vendor}',                [VendorController::class, 'destroy'])->name('admin.vendors.destroy');

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
    Route::get('/customers/lookup-by-email',          [CustomerController::class, 'lookupByEmail'])->name('admin.customers.lookup-by-email');
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
Route::prefix('agent')
    ->name('agent.')
    ->middleware(['auth', 'agent'])
    ->group(function () {

        Route::get('/', [AgentDashboardController::class, 'index'])
            ->name('dashboard');

        // Agent Invoices
        Route::get('/invoices',                           [\App\Http\Controllers\Agent\AgentInvoiceController::class, 'index'])       ->name('invoices.index');
        Route::get('/invoices/create',                    [\App\Http\Controllers\Agent\AgentInvoiceController::class, 'create'])      ->name('invoices.create');
        Route::post('/invoices',                          [\App\Http\Controllers\Agent\AgentInvoiceController::class, 'store'])       ->name('invoices.store');
        Route::get('/invoices/{invoice}',                 [\App\Http\Controllers\Agent\AgentInvoiceController::class, 'show'])        ->name('invoices.show');
        Route::get('/invoices/{invoice}/edit',            [\App\Http\Controllers\Agent\AgentInvoiceController::class, 'edit'])        ->name('invoices.edit');
        Route::put('/invoices/{invoice}',                 [\App\Http\Controllers\Agent\AgentInvoiceController::class, 'update'])      ->name('invoices.update');
        Route::patch('/invoices/{invoice}/status',        [\App\Http\Controllers\Agent\AgentInvoiceController::class, 'toggleStatus'])->name('invoices.toggle-status');
        Route::delete('/invoices/{invoice}',              [\App\Http\Controllers\Agent\AgentInvoiceController::class, 'destroy'])     ->name('invoices.destroy');

        // Agent Quick Customer
        Route::post('/customers/quick-store',             [\App\Http\Controllers\Agent\AgentInvoiceController::class, 'storeCustomer'])->name('customers.store');
        Route::get('/customers/lookup-by-email',          [\App\Http\Controllers\Agent\AgentInvoiceController::class, 'lookupCustomer'])->name('customers.lookup-by-email');

    });
