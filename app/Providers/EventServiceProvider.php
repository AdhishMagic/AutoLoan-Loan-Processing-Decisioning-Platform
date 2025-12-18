<?php

namespace App\Providers;

use App\Events\LoanApplicationSubmitted;
use App\Events\LoanStatusUpdated;
use App\Listeners\NotifyApplicant;
use App\Listeners\NotifyUnderwriters;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, list<class-string>>
     */
    protected $listen = [
        LoanApplicationSubmitted::class => [
            NotifyApplicant::class,
            NotifyUnderwriters::class,
        ],
        LoanStatusUpdated::class => [
            NotifyApplicant::class,
        ],
    ];
}
