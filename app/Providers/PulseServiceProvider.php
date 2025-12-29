<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Facades\Pulse as PulseFacade;
use Laravel\Pulse\Pulse;

final class PulseServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('viewPulse', static function (User $user): bool {
            return $user->isAdmin() || $user->isLoanOfficer();
        });

        // Disable Gravatar to prevent tracking prevention warnings
        PulseFacade::user(function ($user) {
            return (object) [
                'name' => $user->name ?? 'Unknown',
                'extra' => $user->email ?? '',
                'avatar' => null, // Disable avatars to avoid Gravatar tracking
            ];
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
