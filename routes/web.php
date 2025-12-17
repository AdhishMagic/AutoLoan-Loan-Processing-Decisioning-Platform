<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\LoanApplicationController;
use App\Http\Controllers\LoanApprovalController;
use App\Http\Controllers\Auth\GoogleAuthController;
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
        // Place specific routes BEFORE the resource to avoid collisions with {loan}
        Route::delete('loans/bulk-destroy', [LoanApplicationController::class, 'bulkDestroy'])->name('loans.bulk-destroy');
        Route::post('loans/{loan}/save-draft', [LoanApplicationController::class, 'saveDraft'])->name('loans.save-draft');
        Route::resource('loans', LoanApplicationController::class);
        // Step-based Wizard Routes
        Route::get('loans/{loan}/step/{step}', [LoanApplicationController::class, 'showStep'])->name('loans.step.show');
        Route::post('loans/{loan}/step/{step}', [LoanApplicationController::class, 'storeStep'])->name('loans.step.store');
        Route::get('/user/applications', function () {
            // Redirect to loans index for consistency
            return redirect()->route('loans.index');
        })->name('user.applications');
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

// Social Auth - Google
Route::middleware('guest')->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});
