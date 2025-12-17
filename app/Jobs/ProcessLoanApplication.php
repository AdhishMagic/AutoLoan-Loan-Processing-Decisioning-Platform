<?php

namespace App\Jobs;

use App\Models\LoanApplication;
use App\Notifications\LoanStatusNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessLoanApplication implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $loan;

    public function __construct(LoanApplication $loan)
    {
        $this->loan = $loan;
    }

    public function handle(): void
    {
        // 1️⃣ Mark as processing
        $this->loan->update(['status' => 'processing']);

        // 2️⃣ Simulate OCR
        sleep(2);

        // 3️⃣ Simulate KYC check
        sleep(2);

        // 4️⃣ Simulate credit check
        sleep(2);

        // 5️⃣ Update final status
        $this->loan->update(['status' => 'under_review']);

        // 6️⃣ Notify user
        $this->loan->user->notify(
            new LoanStatusNotification($this->loan)
        );
    }
}
