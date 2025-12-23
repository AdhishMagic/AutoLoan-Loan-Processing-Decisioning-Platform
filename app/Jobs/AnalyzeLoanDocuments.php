<?php

namespace App\Jobs;

use App\Models\LoanApplication;
use App\Services\DocumentVerification\DocumentVerificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeLoanDocuments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $loanId;

    public function __construct(string $loanId)
    {
        $this->loanId = $loanId;
    }

    public function handle(DocumentVerificationService $service): void
    {
        /** @var LoanApplication|null $loan */
        $loan = LoanApplication::query()->where('id', $this->loanId)->first();
        if (! $loan) {
            Log::warning('AnalyzeLoanDocuments: loan not found', ['loan_id' => $this->loanId]);
            return;
        }

        // This persists extracted fields + scores back to loan_documents.
        $service->analyzeLoan($loan);
    }
}
