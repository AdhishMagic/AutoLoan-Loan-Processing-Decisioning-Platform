<?php

namespace App\Listeners;

use App\Events\LoanApproved;
use App\Notifications\LoanStatusNotification;
use Illuminate\Support\Facades\Notification;

class SendLoanApprovedNotification
{
    public function handle(LoanApproved $event): void
    {
        $loan = $event->loan->loadMissing(['user', 'assignedOfficer', 'approver']);

        $recipients = collect([$loan->user, $loan->assignedOfficer, $loan->approver])
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
                'Your loan application has been approved.',
                'Loan approved'
            )
        );
    }
}
