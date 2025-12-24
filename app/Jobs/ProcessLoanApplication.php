<?php

namespace App\Jobs;

use App\Models\LoanApplication;
use App\Models\LoanDecision;
use App\Models\UnderwritingRule;
use App\Notifications\LoanStatusNotification;
use App\Services\Underwriting\UnderwritingEngine;
use App\Services\Underwriting\UnderwritingFactsBuilder;
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
        // Idempotency: if underwriting already executed for this loan, don't create duplicate decisions.
        $existingSystemDecision = LoanDecision::query()
            ->where('loan_application_id', $this->loan->id)
            ->where('source', 'SYSTEM')
            ->where('engine_name', UnderwritingEngine::DEFAULT_ENGINE_NAME)
            ->whereNotNull('executed_at')
            ->latest('executed_at')
            ->first();

        if ($existingSystemDecision) {
            return;
        }

        // 1️⃣ Mark as processing
        $this->loan->update(['status' => 'PROCESSING']);

        // 2️⃣ Simulate OCR
        sleep(2);

        // 3️⃣ Simulate KYC check
        sleep(2);

        // 4️⃣ Simulate credit check
        sleep(2);

        // 5️⃣ Run automated underwriting
        $rule = UnderwritingRule::query()->where('active', true)->orderByDesc('id')->first();
        $engine = new UnderwritingEngine();
        $facts = (new UnderwritingFactsBuilder())->build($this->loan);

        $result = $engine->evaluate($rule?->rules_json ?? [], $facts, $rule);

        LoanDecision::create([
            'loan_application_id' => $this->loan->id,
            // Keep `decision` compatible with existing manual flow.
            'decision' => $result->decision,
            'remarks' => 'Automated underwriting decision',
            'decided_by' => null,
            'source' => 'SYSTEM',
            'engine_name' => UnderwritingEngine::DEFAULT_ENGINE_NAME,
            'engine_version' => UnderwritingEngine::DEFAULT_ENGINE_VERSION,
            'underwriting_rule_id' => $rule?->id,
            'underwriting_rule_name' => $rule?->name,
            'underwriting_rule_snapshot' => $rule?->rules_json,
            'facts_snapshot' => $facts,
            'score' => $result->score,
            'decision_status' => $result->decision,
            'reasons' => $result->reasons,
            'trace' => $result->trace,
            'executed_at' => now(),
        ]);

        // 6️⃣ Update status based on automated outcome
        $nextStatus = match ($result->decision) {
            'APPROVE' => 'PENDING_APPROVAL',
            'REJECT' => 'REJECTED',
            default => 'UNDER_REVIEW',
        };

        $this->loan->update(['status' => $nextStatus]);

        // 7️⃣ Notify user
        $this->loan->user->notify(
            new LoanStatusNotification($this->loan)
        );
    }
}
