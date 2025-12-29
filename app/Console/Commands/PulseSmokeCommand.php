<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\PulseSmokeJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Pulse\Pulse;

final class PulseSmokeCommand extends Command
{
    protected $signature = 'pulse:smoke
        {--dispatch-job : Dispatch a smoke job to the default queue}
        {--slow-job : Dispatch a slow smoke job (> Slow Jobs threshold)}';

    protected $description = 'Generate a small amount of Pulse data (cache, slow query, optional queue job) for verification.';

    public function handle(): int
    {
        if (! (bool) config('pulse.enabled')) {
            $this->warn('Pulse is disabled (pulse.enabled=false).');
            return self::SUCCESS;
        }

        // Cache: miss + hit
        Cache::forget('loan:status:pulse-smoke');
        Cache::remember('loan:status:pulse-smoke', 60, static fn (): string => 'ok');
        Cache::get('loan:status:pulse-smoke');

        // DB: force a slow query (>= 1s) if possible
        $driver = DB::connection()->getDriverName();

        match ($driver) {
            'pgsql' => DB::select('select pg_sleep(1)'),
            'mysql', 'mariadb' => DB::select('select sleep(1)'),
            default => $this->warn("Skipping slow-query smoke: unsupported DB driver [{$driver}]."),
        };

        // Exception: report a handled exception
        app(Pulse::class)->report(new \RuntimeException('Pulse smoke exception (handled).'));

        if ((bool) $this->option('dispatch-job') || (bool) $this->option('slow-job')) {
            $sleepMs = (bool) $this->option('slow-job') ? 1500 : 0;
            PulseSmokeJob::dispatch($sleepMs);
            $this->info('Dispatched PulseSmokeJob (run `php artisan queue:work --once` to process).');
        }

        // Explicitly ingest so this works even when a console command exits unexpectedly.
        $count = app(Pulse::class)->ingest();

        $this->info("Pulse smoke generated. Ingested {$count} item(s).");

        return self::SUCCESS;
    }
}
