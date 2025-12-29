<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\KycCheck;
use App\Repositories\Contracts\KycRepositoryInterface;

final class KycRepository implements KycRepositoryInterface
{
    public function latestForLoan(string $loanId): ?KycCheck
    {
        return KycCheck::query()
            ->where('loan_application_id', $loanId)
            ->latest('created_at')
            ->first();
    }

    public function isVerified(string $loanId): bool
    {
        $latest = $this->latestForLoan($loanId);

        return strtoupper((string) ($latest?->result ?? '')) === 'VERIFIED';
    }

    public function createResult(
        string $loanId,
        string $kycType,
        string $result,
        ?int $verifiedBy = null,
    ): KycCheck {
        return KycCheck::query()->create([
            'loan_application_id' => $loanId,
            'kyc_type' => $kycType,
            'result' => $result,
            'verified_by' => $verifiedBy,
        ]);
    }
}
