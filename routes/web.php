<?php

use App\Http\Controllers\Web\AdminUserController;
use App\Http\Controllers\Web\LoanApplicationController;
use App\Http\Controllers\Web\LoanApprovalController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Web\OfficerController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\RoleController;
use App\Http\Controllers\Web\SupportController;
use App\Http\Controllers\Web\UnderwritingRuleController;
use App\Http\Controllers\LoanDocumentController;
use App\Http\Controllers\NotificationController;
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
        Route::get('officer/loans/{loan}', [OfficerController::class, 'show'])->name('officer.loans.show');
        Route::get('officer/loans/{loan}/decision', [OfficerController::class, 'decision'])->name('officer.loans.decision');
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

        // Admin loans overview
        Route::get('admin/loans', [\App\Http\Controllers\Web\AdminLoanController::class, 'index'])->name('admin.loans.index');
        Route::get('admin/loans/{loan}', [\App\Http\Controllers\Web\AdminLoanController::class, 'show'])->name('admin.loans.show');

        // Underwriting rule management (admin)
        Route::get('admin/underwriting/rules', [UnderwritingRuleController::class, 'index'])->name('underwriting.rules.index');
        Route::get('admin/underwriting/rules/{rule}/edit', [UnderwritingRuleController::class, 'edit'])->name('underwriting.rules.edit');
        Route::put('admin/underwriting/rules/{rule}', [UnderwritingRuleController::class, 'update'])->name('underwriting.rules.update');
        Route::post('admin/underwriting/rules/{rule}/activate', [UnderwritingRuleController::class, 'activate'])->name('underwriting.rules.activate');
        Route::post('admin/underwriting/rules/{rule}/deactivate', [UnderwritingRuleController::class, 'deactivate'])->name('underwriting.rules.deactivate');
        Route::post('admin/underwriting/rules/{rule}/test', [UnderwritingRuleController::class, 'test'])->name('underwriting.rules.test');
    });

    // LOAN OFFICER (manager role) - manage rules (create/update/activate/test)
    Route::middleware('role:manager')->group(function () {
        Route::get('officer/underwriting/rules', [UnderwritingRuleController::class, 'index'])->name('officer.underwriting.rules.index');
        Route::get('officer/underwriting/rules/create', [UnderwritingRuleController::class, 'create'])->name('officer.underwriting.rules.create');
        Route::post('officer/underwriting/rules', [UnderwritingRuleController::class, 'store'])->name('officer.underwriting.rules.store');
        Route::get('officer/underwriting/rules/{rule}/edit', [UnderwritingRuleController::class, 'edit'])->name('officer.underwriting.rules.edit');
        Route::put('officer/underwriting/rules/{rule}', [UnderwritingRuleController::class, 'update'])->name('officer.underwriting.rules.update');
        Route::post('officer/underwriting/rules/{rule}/activate', [UnderwritingRuleController::class, 'activate'])->name('officer.underwriting.rules.activate');
        Route::post('officer/underwriting/rules/{rule}/deactivate', [UnderwritingRuleController::class, 'deactivate'])->name('officer.underwriting.rules.deactivate');
        Route::post('officer/underwriting/rules/{rule}/test', [UnderwritingRuleController::class, 'test'])->name('officer.underwriting.rules.test');
    });

    // Loan Documents (policy-controlled)
    Route::post('loans/{loan}/documents', [LoanDocumentController::class, 'store'])->name('loan.document.upload');
    Route::get('loan-documents/{document}/download-link', [LoanDocumentController::class, 'signedDownloadLink'])->name('loan.document.signed-link');
    Route::get('loan-documents/{document}/download', [LoanDocumentController::class, 'download'])
        ->middleware('signed')
        ->name('loan.document.download');

    Route::get('notifications/{notification}/open', [NotificationController::class, 'open'])->name('notifications.open');

    // API Key Management (Sanctum Personal Access Tokens)
    Route::get('/dashboard/api-keys', [\App\Http\Controllers\ApiKeyController::class, 'index'])->name('api-keys.index');
    Route::post('/dashboard/api-keys', [\App\Http\Controllers\ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::delete('/dashboard/api-keys/{token}', [\App\Http\Controllers\ApiKeyController::class, 'destroy'])->name('api-keys.destroy');

    // OTP actions for API access verification
    Route::post('/dashboard/api-keys/otp/send', [\App\Http\Controllers\Web\ApiOtpController::class, 'send'])->name('api-keys.otp.send');
    Route::post('/dashboard/api-keys/otp/verify', [\App\Http\Controllers\Web\ApiOtpController::class, 'verify'])->name('api-keys.otp.verify');
});

require __DIR__.'/auth.php';

// Social Auth - Google
Route::middleware('guest')->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});
