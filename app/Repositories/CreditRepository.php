<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CreditCheck;
use App\Repositories\Contracts\CreditRepositoryInterface;

final class CreditRepository implements CreditRepositoryInterface
{
    public function latestForLoan(string $loanId): ?CreditCheck
    {
        return CreditCheck::query()
            ->where('loan_application_id', $loanId)
            ->latest('created_at')
            ->first();
    }

    public function latestScoreForLoan(string $loanId): ?int
    {
        $latest = $this->latestForLoan($loanId);

        if ($latest?->credit_score === null) {
            return null;
        }

        return (int) $latest->credit_score;
    }

    public function storeResult(
        string $loanId,
        ?int $creditScore,
        ?string $riskLevel,
        ?string $source,
    ): CreditCheck {
        return CreditCheck::query()->create([
            'loan_application_id' => $loanId,
            'credit_score' => $creditScore,
            'risk_level' => $riskLevel,
            'source' => $source,
        ]);
    }
}
