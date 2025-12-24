<?php

namespace App\Services\Underwriting;

class UnderwritingResult
{
    /**
     * @param array<int, array<string, mixed>> $reasons
     * @param array<int, array<string, mixed>> $trace
     */
    public function __construct(
        public string $decision,
        public int $score,
        public array $reasons = [],
        public array $trace = [],
        public ?int $underwritingRuleId = null,
        public ?string $underwritingRuleName = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'decision' => $this->decision,
            'score' => $this->score,
            'reasons' => $this->reasons,
            'trace' => $this->trace,
            'underwriting_rule_id' => $this->underwritingRuleId,
            'underwriting_rule_name' => $this->underwritingRuleName,
        ];
    }
}
