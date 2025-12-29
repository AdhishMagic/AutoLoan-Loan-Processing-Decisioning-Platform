<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\CreditCheck;

interface CreditRepositoryInterface
{
    public function latestForLoan(string $loanId): ?CreditCheck;

    public function latestScoreForLoan(string $loanId): ?int;

    public function storeResult(
        string $loanId,
        ?int $creditScore,
        ?string $riskLevel,
        ?string $source,
    ): CreditCheck;
}
