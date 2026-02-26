<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\Merchants\MerchantController;
use App\Http\Controllers\Admin\Users\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Merchant\FormFields\FormFieldController;
use App\Http\Controllers\Merchant\Orders\OrderController;
use App\Http\Controllers\Merchant\Submissions\SubmissionController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// Authentication Routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

// Protected Routes (authenticated users only)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Merchant Routes
    Route::prefix('merchant')->name('merchant.')->middleware('merchant')->group(function () {
        // Form Fields
        Route::get('/form-fields', [FormFieldController::class, 'index'])->name('form-fields.index');
        Route::post('/form-fields', [FormFieldController::class, 'store'])->name('form-fields.store');
        Route::get('/form-fields/list', [FormFieldController::class, 'list'])->name('form-fields.list');
        Route::patch('/form-fields/{formField}/status', [FormFieldController::class, 'updateStatus'])->name('form-fields.update-status');
        Route::post('/form-fields/bulk-status', [FormFieldController::class, 'bulkUpdateStatus'])->name('form-fields.bulk-update-status');
        Route::post('/form-fields/bulk-delete', [FormFieldController::class, 'bulkDestroy'])->name('form-fields.bulk-destroy');
        Route::delete('/form-fields/{formField}', [FormFieldController::class, 'destroy'])->name('form-fields.destroy');

        // Orders (form submission data with processing)
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/list', [OrderController::class, 'list'])->name('orders.list');
        Route::patch('/orders/{submission}/process', [OrderController::class, 'process'])->name('orders.process');

        // Submissions
        Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/list', [SubmissionController::class, 'list'])->name('submissions.list');
        Route::post('/submissions/bulk-delete', [SubmissionController::class, 'bulkDestroy'])->name('submissions.bulk-destroy');
        Route::delete('/submissions/{submission}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Merchants
        Route::get('/merchants', [MerchantController::class, 'index'])->name('merchants.index');
        Route::get('/merchants/list', [MerchantController::class, 'list'])->name('merchants.list');
        Route::post('/merchants/bulk-delete', [MerchantController::class, 'bulkDestroy'])->name('merchants.bulk-destroy');
        Route::delete('/merchants/{merchant}', [MerchantController::class, 'destroy'])->name('merchants.destroy');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/list', [UserController::class, 'list'])->name('users.list');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
        Route::post('/users/bulk-status', [UserController::class, 'bulkUpdateStatus'])->name('users.bulk-update-status');
        Route::post('/users/bulk-delete', [UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
