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
        $loan = $event->loan->loadMissing('user');

        if (empty($loan->user?->email)) {
            return;
        }

        Mail::to($loan->user->email)->queue(new LoanSubmittedMail($loan));
    }
}
