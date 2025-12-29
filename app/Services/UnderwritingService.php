<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Pure underwriting decision engine.
 *
 * Inputs are numeric and fully validated upstream.
 */
final class UnderwritingService
{
    public const DECISION_APPROVED = 'approved';
    public const DECISION_MANUAL_REVIEW = 'manual_review';
    public const DECISION_REJECTED = 'rejected';

    /**
     * @param int $creditScore Credit score (typically 300-900).
     * @param float $monthlyIncome Monthly income in INR.
     * @param float $dti Debt-to-income ratio as fraction (e.g. 0.35 for 35%).
     */
    public function decide(int $creditScore, float $monthlyIncome, float $dti): string
    {
        if ($monthlyIncome < 15000.0) {
            return self::DECISION_MANUAL_REVIEW;
        }

        if ($creditScore < 550) {
            return self::DECISION_REJECTED;
        }

        if ($creditScore < 650) {
            return self::DECISION_MANUAL_REVIEW;
        }

        if ($dti > 0.55) {
            return self::DECISION_REJECTED;
        }

        if ($dti >= 0.40) {
            return self::DECISION_MANUAL_REVIEW;
        }

        return self::DECISION_APPROVED;
    }
}
