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
        LoanDocument::class => \App\Policies\LoanDocumentPolicy::class,
    ];

    public function boot(): void
    {
        Gate::define('use-analyzer', function ($user): bool {
            return $user->isLoanOfficer() || $user->isAdmin();
        });
    }
}
