<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class LoanCacheService
{
    private const LOAN_STATUS_TTL_SECONDS = 300; // 5 minutes
    private const USER_PROFILE_TTL_SECONDS = 900; // 15 minutes
    private const KYC_RESULT_TTL_SECONDS = 300; // 5 minutes

    public function getLoanStatus(string $loanId, callable $callback): mixed
    {
        return Cache::remember(
            $this->loanStatusKey($loanId),
            self::LOAN_STATUS_TTL_SECONDS,
            $callback
        );
    }

    public function forgetLoanStatus(string $loanId): bool
    {
        return Cache::forget($this->loanStatusKey($loanId));
    }

    public function getUserProfile(int $userId, callable $callback): mixed
    {
        return Cache::remember(
            $this->userProfileKey($userId),
            self::USER_PROFILE_TTL_SECONDS,
            $callback
        );
    }

    public function forgetUserProfile(int $userId): bool
    {
        return Cache::forget($this->userProfileKey($userId));
    }

    public function getKycResult(string $loanId, callable $callback): mixed
    {
        return Cache::remember(
            $this->kycResultKey($loanId),
            self::KYC_RESULT_TTL_SECONDS,
            $callback
        );
    }

    public function forgetKycResult(string $loanId): bool
    {
        return Cache::forget($this->kycResultKey($loanId));
    }

    private function loanStatusKey(string $loanId): string
    {
        return 'loan:status:' . $loanId;
    }

    private function userProfileKey(int $userId): string
    {
        return 'user:profile:' . $userId;
    }

    private function kycResultKey(string $loanId): string
    {
        return 'kyc:result:' . $loanId;
    }
}
