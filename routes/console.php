<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonInterval;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('loan:verify-documents {loanId : Loan application UUID}', function (string $loanId) {
    /** @var \App\Models\LoanApplication|null $loan */
    $loan = \App\Models\LoanApplication::query()->where('id', $loanId)->first();
    if (! $loan) {
        $this->error('Loan not found.');
        return 1;
    }

    /** @var \App\Services\DocumentVerification\DocumentVerificationService $service */
    $service = app(\App\Services\DocumentVerification\DocumentVerificationService::class);
    $result = $service->analyzeLoan($loan);

    $this->line($result['report']);

    return 0;
})->purpose('Analyze OCR text of uploaded loan documents and print a Loan Officer-friendly verification report.');

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

// Pulse: server heartbeat (drives Servers / worker health widgets)
Schedule::command('pulse:check')->everyMinute()->name('pulse-check');

// Pulse: daily retention trimming (free-tier friendly DB storage)
Schedule::call(function () {
    if (! Schema::hasTable('pulse_entries') || ! Schema::hasTable('pulse_values') || ! Schema::hasTable('pulse_aggregates')) {
        return;
    }

    $keep = (string) config('pulse.storage.trim.keep', '7 days');
    $interval = CarbonInterval::make($keep) ?? CarbonInterval::days(7);
    $cutoff = now()->sub($interval)->getTimestamp();

    DB::table('pulse_entries')->where('timestamp', '<', $cutoff)->delete();
    DB::table('pulse_values')->where('timestamp', '<', $cutoff)->delete();
    DB::table('pulse_aggregates')->where('bucket', '<', $cutoff)->delete();
})->dailyAt('02:30')->name('pulse-trim');
