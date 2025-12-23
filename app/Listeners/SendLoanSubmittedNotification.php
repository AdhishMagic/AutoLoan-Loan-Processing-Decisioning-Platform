<?php

namespace App\Listeners;

use App\Events\LoanSubmitted;
use App\Models\User;
use App\Notifications\LoanStatusNotification;
use Illuminate\Support\Facades\Notification;

class SendLoanSubmittedNotification
{
    public function handle(LoanSubmitted $event): void
    {
        $loan = $event->loan->loadMissing(['user', 'assignedOfficer']);

        $recipients = [];

        if ($loan->user) {
            $recipients[] = $loan->user;
        }

        if ($loan->assignedOfficer) {
            $recipients[] = $loan->assignedOfficer;
        }

        // Fallback: if no assigned officer yet, notify all loan officers (manager role)
        if (empty($loan->assigned_officer_id)) {
            $managers = User::query()
                ->whereHas('role', fn ($q) => $q->where('name', 'manager'))
                ->get();

            foreach ($managers as $manager) {
                $recipients[] = $manager;
            }
        }

        $recipients = collect($recipients)->filter()->unique('id')->values();

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send(
            $recipients,
            new LoanStatusNotification(
                $loan,
                'A loan application has been submitted and is ready for review.',
                'Loan submitted'
            )
        );
    }
}
