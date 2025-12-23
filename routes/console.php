<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduling (concept + logic): daily maintenance
// Use-case: keep API access tokens tidy by pruning expired tokens.
Schedule::call(function () {
    if (! Schema::hasTable('personal_access_tokens')) {
        return;
    }

    if (! Schema::hasColumn('personal_access_tokens', 'expires_at')) {
        return;
    }

    DB::table('personal_access_tokens')
        ->whereNotNull('expires_at')
        ->where('expires_at', '<', now())
        ->delete();
})->dailyAt('02:00')->name('prune-expired-api-tokens');
