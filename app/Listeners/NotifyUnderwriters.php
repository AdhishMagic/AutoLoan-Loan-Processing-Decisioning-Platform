<?php

namespace App\Listeners;

use App\Events\LoanApplicationSubmitted;
use App\Mail\LoanReceivedForVerificationMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyUnderwriters implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(LoanApplicationSubmitted $event): void
    {
        $loan = $event->loan->loadMissing(['user', 'applicants']);

        $officers = User::query()
            ->whereHas('role', fn ($q) => $q->where('name', 'manager'))
            ->whereNotNull('email')
            ->pluck('email')
            ->all();

        if (!empty($officers)) {
            $primary = array_shift($officers);
            // Send one email with BCC to all underwriters, and delay slightly to avoid Mailtrap per-second cap.
            Mail::to($primary)
                ->bcc($officers)
                ->later(now()->addSeconds(2), (new LoanReceivedForVerificationMail($loan))->onConnection('database'));
        }
    }
}
