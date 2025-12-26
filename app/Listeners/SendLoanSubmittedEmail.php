<?php

namespace App\Listeners;

use App\Events\LoanSubmitted;
use App\Mail\LoanSubmittedMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendLoanSubmittedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(LoanSubmitted $event): void
    {
        $loan = $event->loan->loadMissing(['user', 'assignedOfficer']);

        $emails = [];

        if (! empty($loan->user?->email)) {
            $emails[] = $loan->user->email;
        }

        if (! empty($loan->assignedOfficer?->email)) {
            $emails[] = $loan->assignedOfficer->email;
        }

        // Fallback: if no assigned officer yet, notify all loan officers (manager role)
        if (empty($loan->assigned_officer_id)) {
            $managerEmails = User::query()
                ->whereHas('role', fn ($q) => $q->where('name', 'manager'))
                ->whereNotNull('email')
                ->pluck('email')
                ->all();

            $emails = array_merge($emails, $managerEmails);
        }

        $emails = array_values(array_unique(array_filter($emails)));

        if (empty($emails)) {
            return;
        }

        // Send one email with BCC to avoid queuing multiple jobs per recipient.
        $primary = array_shift($emails);
        $delay = (int) env('MAIL_THROTTLE_SECONDS', 2);
        Mail::to($primary)
            ->bcc($emails)
            ->later(now()->addSeconds($delay), (new LoanSubmittedMail($loan))->onConnection('database'));
    }
}
