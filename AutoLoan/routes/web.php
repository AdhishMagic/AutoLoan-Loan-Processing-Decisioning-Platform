<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\LoanApplicationController;
use App\Http\Controllers\LoanApprovalController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // USER
    Route::middleware('role:user')->group(function () {
        // Optional: expose a real applications page
        Route::view('/user/applications', 'user.applications')->name('user.applications');
        // Route::resource('loans', LoanApplicationController::class); // enable when controller methods are ready
    });

    // LOAN OFFICER (manager role)
    Route::middleware('role:manager')->group(function () {
        Route::get('officer/review', [OfficerController::class, 'index'])->name('officer.review');
        Route::post('loans/{loan}/approve', [LoanApprovalController::class, 'approve'])->name('loans.approve');
        Route::post('loans/{loan}/reject', [LoanApprovalController::class, 'reject'])->name('loans.reject');
        Route::post('loans/{loan}/hold', [LoanApprovalController::class, 'hold'])->name('loans.hold');
    });

    // CUSTOMER SUPPORT
    Route::middleware('role:customer_service')->group(function () {
        Route::get('support/loans', [SupportController::class, 'index'])->name('support.loans.index');
    });

    // ADMIN
    Route::middleware('role:admin')->group(function () {
        Route::resource('admin/users', AdminUserController::class)->names('admin.users');
        Route::resource('admin/roles', RoleController::class)->names('admin.roles');
    });
});

require __DIR__.'/auth.php';
