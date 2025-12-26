<?php

namespace App\Providers;

use App\Models\LoanApplication;
use App\Models\LoanDocument;
use App\Policies\LoanApplicationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    protected $policies = [
        LoanApplication::class => LoanApplicationPolicy::class,
        LoanDocument::class => \App\Http\Policies\LoanDocumentPolicy::class,
    ];

    public function boot(): void
    {
        // Allow admin to perform any ability without explicit policy checks
        Gate::before(function ($user, string $ability = null) {
            return method_exists($user, 'isAdmin') && $user->isAdmin() ? true : null;
        });

        Gate::define('use-analyzer', function ($user): bool {
            return $user->isLoanOfficer() || $user->isAdmin();
        });
    }
}
