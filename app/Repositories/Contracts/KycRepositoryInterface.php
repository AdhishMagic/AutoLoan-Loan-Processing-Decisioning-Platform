<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\KycCheck;

interface KycRepositoryInterface
{
    public function latestForLoan(string $loanId): ?KycCheck;

    public function isVerified(string $loanId): bool;

    public function createResult(
        string $loanId,
        string $kycType,
        string $result,
        ?int $verifiedBy = null,
    ): KycCheck;
}
