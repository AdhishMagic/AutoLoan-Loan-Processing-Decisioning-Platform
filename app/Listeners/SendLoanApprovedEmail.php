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
        $loan = $event->loan->loadMissing('user');

        if (empty($loan->user?->email)) {
            return;
        }

        Mail::to($loan->user->email)->queue(new LoanApprovedMail($loan));
    }
}
