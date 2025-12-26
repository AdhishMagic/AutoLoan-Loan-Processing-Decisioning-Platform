<?php

namespace App\Listeners;

use App\Events\LoanSubmitted;
use App\Mail\LoanSubmittedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendLoanSubmittedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(LoanSubmitted $event): void
    {
        $loan = $event->loan->loadMissing(['user', 'assignedOfficer']);

        $recipient = $loan->user?->email;
        if (! is_string($recipient) || trim($recipient) === '') {
            return;
        }

        // Send only to the applicant to avoid provider throttles.
        $delay = (int) env('MAIL_THROTTLE_SECONDS', 2);

        Mail::to($recipient)
            ->later(now()->addSeconds($delay), (new LoanSubmittedMail($loan))->onConnection('database'));
    }
}
