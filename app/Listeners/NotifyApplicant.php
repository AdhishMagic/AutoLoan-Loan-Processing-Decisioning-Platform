<?php

namespace App\Listeners;

use App\Events\LoanApplicationSubmitted;
use App\Events\LoanStatusUpdated;
use App\Mail\ApplicationSubmittedMail;
use App\Mail\LoanApprovedMail;
use App\Mail\LoanRejectedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyApplicant implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(LoanApplicationSubmitted|LoanStatusUpdated $event): void
    {
        if ($event instanceof LoanApplicationSubmitted) {
            $loan = $event->loan->loadMissing('user');

            if (!empty($loan->user?->email)) {
                Mail::to($loan->user->email)->queue(new ApplicationSubmittedMail($loan));
            }

            return;
        }

        $loan = $event->loan->loadMissing('user');
        $newStatus = strtoupper($event->newStatus);

        if (empty($loan->user?->email)) {
            return;
        }

        if ($newStatus === 'APPROVED') {
            Mail::to($loan->user->email)->queue(new LoanApprovedMail($loan));
        } elseif ($newStatus === 'REJECTED') {
            Mail::to($loan->user->email)->queue(new LoanRejectedMail($loan));
        }
    }
}
