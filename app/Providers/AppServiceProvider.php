<?php

namespace App\Providers;

use App\Models\Applicant;
use App\Models\LoanApplication;
use App\Models\LoanDocument;
use App\Models\User;
use App\Observers\ApplicantObserver;
use App\Observers\LoanApplicationObserver;
use App\Observers\LoanDocumentObserver;
use App\Observers\UserObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('mail', function () {
            return Limit::perSecond(1);
        });

        $this->loadMigrationsFrom([
            database_path('migrations/users'),
            database_path('migrations/roles'),
            database_path('migrations/loans'),
            database_path('migrations/loan_documents'),
            database_path('migrations/notifications'),
            database_path('migrations/kyc_checks'),
            database_path('migrations/credit_checks'),
            database_path('migrations/underwriting_rules'),
            database_path('migrations/audit_logs'),
        ]);

        // Register model observers
        LoanApplication::observe(LoanApplicationObserver::class);
        LoanDocument::observe(LoanDocumentObserver::class);

        Applicant::observe(ApplicantObserver::class);
        User::observe(UserObserver::class);

        // Blade role helpers: @role('admin') ... @endrole, @anyrole('admin','manager')
        Blade::if('role', function (string $role): bool {
            return auth()->check() && auth()->user()->role?->name === $role;
        });

        Blade::if('anyrole', function (...$roles): bool {
            return auth()->check() && in_array(auth()->user()->role?->name, $roles, true);
        });
    }
}
