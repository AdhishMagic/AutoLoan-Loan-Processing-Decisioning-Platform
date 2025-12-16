<?php

namespace App\Providers;

use App\Models\LoanApplication;
use App\Models\LoanDocument;
use App\Observers\LoanApplicationObserver;
use App\Observers\LoanDocumentObserver;
use Illuminate\Support\Facades\Blade;
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
        // Register model observers
        LoanApplication::observe(LoanApplicationObserver::class);
        LoanDocument::observe(LoanDocumentObserver::class);

        // Blade role helpers: @role('admin') ... @endrole, @anyrole('admin','manager')
        Blade::if('role', function (string $role): bool {
            return auth()->check() && auth()->user()->role?->name === $role;
        });

        Blade::if('anyrole', function (...$roles): bool {
            return auth()->check() && in_array(auth()->user()->role?->name, $roles, true);
        });
    }
}
