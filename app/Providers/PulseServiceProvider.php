<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Pulse;

final class PulseServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('viewPulse', static function (User $user): bool {
            return $user->isAdmin() || $user->isLoanOfficer();
        });

        $this->app->terminating(function (): void {
            if (! $this->app->runningInConsole() || ! (bool) config('pulse.enabled')) {
                return;
            }

            $pulse = $this->app->make(Pulse::class);

            if ($pulse->wantsIngesting()) {
                $pulse->ingest();
            }
        });
    }
}
