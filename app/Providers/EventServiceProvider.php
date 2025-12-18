<?php

namespace App\Providers;

use App\Events\LoanApproved;
use App\Events\LoanApplicationSubmitted;
use App\Events\LoanRejected;
use App\Events\LoanSubmitted;
use App\Events\LoanStatusUpdated;
use App\Listeners\SendLoanApprovedEmail;
use App\Listeners\SendLoanRejectedEmail;
use App\Listeners\SendLoanSubmittedEmail;
use App\Listeners\NotifyUnderwriters;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function register()
    {
        static::disableEventDiscovery();

        parent::register();
    }

    public function shouldDiscoverEvents()
    {
        return false;
    }

    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, list<class-string>>
     */
    protected $listen = [
        LoanApplicationSubmitted::class => [
            NotifyUnderwriters::class,
        ],
        LoanSubmitted::class => [
            SendLoanSubmittedEmail::class,
        ],
        LoanApproved::class => [
            SendLoanApprovedEmail::class,
        ],
        LoanRejected::class => [
            SendLoanRejectedEmail::class,
        ],
    ];
}
