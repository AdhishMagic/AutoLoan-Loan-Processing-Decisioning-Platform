<?php

namespace App\Listeners;

use App\Events\LoanApproved;
use App\Mail\LoanApprovedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendLoanApprovedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(LoanApproved $event): void
    {
        $loan = $event->loan->loadMissing(['user', 'assignedOfficer', 'approver']);

        $emails = [];

        if (! empty($loan->user?->email)) {
            $emails[] = $loan->user->email;
        }

        if (! empty($loan->assignedOfficer?->email)) {
            $emails[] = $loan->assignedOfficer->email;
        }

        if (! empty($loan->approver?->email)) {
            $emails[] = $loan->approver->email;
        }

        $emails = array_values(array_unique(array_filter($emails)));

        if (empty($emails)) {
            return;
        }

        $primary = array_shift($emails);
        $delay = (int) env('MAIL_THROTTLE_SECONDS', 2);
        Mail::to($primary)
            ->bcc($emails)
            ->later(now()->addSeconds($delay), (new LoanApprovedMail($loan))->onConnection('database'));
    }
}
