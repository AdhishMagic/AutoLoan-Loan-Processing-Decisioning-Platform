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
            ->get(['id', 'email']);

        foreach ($officers as $officer) {
            Mail::to($officer->email)->queue(new LoanReceivedForVerificationMail($loan));
        }
    }
}
