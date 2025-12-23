<?php

namespace App\Providers;

use App\Events\LoanApproved;
use App\Events\LoanApplicationSubmitted;
use App\Events\LoanRejected;
use App\Events\LoanSubmitted;
use App\Listeners\SendLoanApprovedNotification;
use App\Listeners\SendLoanRejectedNotification;
use App\Listeners\SendLoanSubmittedNotification;
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
            SendLoanSubmittedNotification::class,
            SendLoanSubmittedEmail::class,
        ],
        LoanApproved::class => [
            SendLoanApprovedNotification::class,
            SendLoanApprovedEmail::class,
        ],
        LoanRejected::class => [
            SendLoanRejectedNotification::class,
            SendLoanRejectedEmail::class,
        ],
    ];
}
