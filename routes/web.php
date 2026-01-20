<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountDepositController;
use App\Http\Controllers\AccountWalletController;
use App\Http\Controllers\AccountOrderController;
use App\Http\Controllers\Admin\DepositController as AdminDepositController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentMethodController as AdminPaymentMethodController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ServiceFormFieldController as AdminServiceFormFieldController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::get('/account/deposits', [AccountDepositController::class, 'index'])->name('account.deposits');
    Route::get('/account/wallet', [AccountWalletController::class, 'index'])->name('account.wallet');
    Route::get('/account/orders', [AccountOrderController::class, 'index'])->name('account.orders');
    Route::get('/account/orders/{order}', [AccountOrderController::class, 'show'])->name('account.orders.show');

    Route::get('/deposit', [DepositController::class, 'index'])->name('deposit.index');
    Route::get('/deposit/{paymentMethod:slug}', [DepositController::class, 'show'])->name('deposit.show');
    Route::post('/deposit/{paymentMethod:slug}', [DepositController::class, 'store'])->name('deposit.store');

    Route::post('/services/{service:slug}/purchase', [ServiceController::class, 'purchase'])->name('services.purchase');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/payment-methods', [AdminPaymentMethodController::class, 'index'])->name('payment-methods.index');
    Route::get('/payment-methods/create', [AdminPaymentMethodController::class, 'create'])->name('payment-methods.create');
    Route::post('/payment-methods', [AdminPaymentMethodController::class, 'store'])->name('payment-methods.store');
    Route::get('/payment-methods/{paymentMethod}/edit', [AdminPaymentMethodController::class, 'edit'])->name('payment-methods.edit');
    Route::put('/payment-methods/{paymentMethod}', [AdminPaymentMethodController::class, 'update'])->name('payment-methods.update');

    Route::get('/deposits', [AdminDepositController::class, 'index'])->name('deposits.index');
    Route::get('/deposits/{depositRequest}', [AdminDepositController::class, 'show'])->name('deposits.show');
    Route::post('/deposits/{depositRequest}/approve', [AdminDepositController::class, 'approve'])->name('deposits.approve');
    Route::post('/deposits/{depositRequest}/reject', [AdminDepositController::class, 'reject'])->name('deposits.reject');
    Route::get('/deposits/{depositRequest}/evidence', [AdminDepositController::class, 'downloadEvidence'])->name('deposits.evidence');

    Route::resource('categories', AdminCategoryController::class)->except(['show', 'destroy']);
    Route::resource('services', AdminServiceController::class)->except(['show', 'destroy']);
    Route::get('services/{service}/fields/create', [AdminServiceFormFieldController::class, 'create'])->name('services.fields.create');
    Route::post('services/{service}/fields', [AdminServiceFormFieldController::class, 'store'])->name('services.fields.store');
    Route::get('services/{service}/fields/{field}/edit', [AdminServiceFormFieldController::class, 'edit'])->name('services.fields.edit');
    Route::put('services/{service}/fields/{field}', [AdminServiceFormFieldController::class, 'update'])->name('services.fields.update');
    Route::delete('services/{service}/fields/{field}', [AdminServiceFormFieldController::class, 'destroy'])->name('services.fields.destroy');
    Route::post('services/{service}/fields/{field}/options', [AdminServiceFormFieldController::class, 'storeOption'])->name('services.fields.options.store');
    Route::delete('services/{service}/fields/{field}/options/{option}', [AdminServiceFormFieldController::class, 'destroyOption'])->name('services.fields.options.destroy');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
});

require __DIR__.'/auth.php';
