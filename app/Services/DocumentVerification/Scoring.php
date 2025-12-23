<?php

namespace App\Services\DocumentVerification;

final class Scoring
{
    public static function clamp(int|float $value, int $min = 0, int $max = 100): int
    {
        $v = (int) round($value);
        if ($v < $min) {
            return $min;
        }
        if ($v > $max) {
            return $max;
        }
        return $v;
    }

    /**
     * Conservative scoring: missing key fields caps the max.
     */
    public static function authenticityScore(array $signals): int
    {
        // Expected keys: hasKeyFields, hasGovKeywords, hasStructuredLabels, matchesUser, ocrWeak
        $score = 50;

        if (! empty($signals['hasKeyFields'])) {
            $score += 20;
        } else {
            $score -= 15;
        }

        if (! empty($signals['hasGovKeywords'])) {
            $score += 10;
        }

        if (! empty($signals['hasStructuredLabels'])) {
            $score += 10;
        }

        if (array_key_exists('matchesUser', $signals)) {
            $score += $signals['matchesUser'] ? 10 : -20;
        }

        if (! empty($signals['ocrWeak'])) {
            $score -= 20;
        }

        return self::clamp($score);
    }

    public static function uniquenessScore(array $signals): int
    {
        // Expected keys: isDuplicateOnLoan, tooShort, hasHash
        $score = 85;

        if (empty($signals['hasHash'])) {
            $score -= 15;
        }

        if (! empty($signals['tooShort'])) {
            $score -= 25;
        }

        if (! empty($signals['isDuplicateOnLoan'])) {
            $score -= 40;
        }

        return self::clamp($score);
    }

    public static function trustScore(int $authenticity, int $uniqueness, array $signals = []): int
    {
        $score = (0.65 * $authenticity) + (0.35 * $uniqueness);

        if (array_key_exists('matchesUser', $signals)) {
            $score += $signals['matchesUser'] ? 5 : -10;
        }

        return self::clamp($score);
    }

    public static function riskLevel(int $trustScore): string
    {
        if ($trustScore >= 70) {
            return 'LOW';
        }
        if ($trustScore >= 50) {
            return 'MEDIUM';
        }
        return 'HIGH';
    }

    public static function recommendation(int $trustScore): string
    {
        if ($trustScore >= 90) {
            return '✅ Auto-Approve';
        }
        if ($trustScore >= 70) {
            return '⚠️ Manual Review Required';
        }
        return '❌ Reject / Re-upload Documents';
    }
}
