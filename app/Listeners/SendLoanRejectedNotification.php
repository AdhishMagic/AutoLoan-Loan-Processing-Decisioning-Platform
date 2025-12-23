<?php

namespace App\Listeners;

use App\Events\LoanRejected;
use App\Notifications\LoanStatusNotification;
use Illuminate\Support\Facades\Notification;

class SendLoanRejectedNotification
{
    public function handle(LoanRejected $event): void
    {
        $loan = $event->loan->loadMissing(['user', 'assignedOfficer', 'rejecter']);

        $recipients = collect([$loan->user, $loan->assignedOfficer, $loan->rejecter])
            ->filter()
            ->unique('id')
            ->values();

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send(
            $recipients,
            new LoanStatusNotification(
                $loan,
                'Your loan application has been rejected.',
                'Loan rejected'
            )
        );
    }
}
