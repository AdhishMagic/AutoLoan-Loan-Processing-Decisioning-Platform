<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $tokenId = $request->user()?->currentAccessToken()?->id;
            $userId = $request->user()?->id;
            $key = $tokenId ? ('token:'.$tokenId) : ($userId ? ('user:'.$userId) : ('ip:'.$request->ip()));

            return Limit::perMinute(60)->by($key);
        });
    }
}
