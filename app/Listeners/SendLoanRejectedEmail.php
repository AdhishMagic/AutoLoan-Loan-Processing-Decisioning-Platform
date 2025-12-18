<?php

namespace App\Listeners;

use App\Events\LoanRejected;
use App\Mail\LoanRejectedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendLoanRejectedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(LoanRejected $event): void
    {
        $loan = $event->loan->loadMissing('user');

        if (empty($loan->user?->email)) {
            return;
        }

        Mail::to($loan->user->email)->queue(new LoanRejectedMail($loan));
    }
}
