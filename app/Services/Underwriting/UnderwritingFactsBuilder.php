<?php

namespace App\Services\Underwriting;

use App\Models\LoanApplication;
use Illuminate\Support\Arr;

class UnderwritingFactsBuilder
{
    /**
     * Build a normalized facts payload used for underwriting rule evaluation.
     *
     * @return array<string, mixed>
     */
    public function build(LoanApplication $loan): array
    {
        $loan->loadMissing([
            'primaryApplicant',
            'applicants.incomeDetails',
            'applicants.employmentDetails',
            'applicants.existingLoans',
            'creditChecks',
            'documents',
        ]);

        $primaryApplicant = $loan->primaryApplicant()->first();

        $applicantDob = $primaryApplicant?->date_of_birth;
        $ageYears = $applicantDob ? (int) $applicantDob->diffInYears(now()) : null;

        $incomeMonthly = null;
        if ($loan->monthly_income !== null) {
            $incomeMonthly = (float) $loan->monthly_income;
        } elseif ($primaryApplicant) {
            $incomeMonthly = (float) ($primaryApplicant->incomeDetails
                ->where('income_frequency', 'MONTHLY')
                ->whereNotNull('net_income_amount')
                ->sum('net_income_amount'));
            if ($incomeMonthly <= 0) {
                $incomeMonthly = null;
            }
        }

        $currentEmploymentType = null;
        $employmentYears = null;
        if ($primaryApplicant) {
            $currentEmployment = $primaryApplicant->employmentDetails
                ->firstWhere('employment_status', 'CURRENT');
            $currentEmploymentType = $currentEmployment?->employment_type;
            $employmentYears = $currentEmployment?->total_experience_years
                ?? $currentEmployment?->current_company_experience_years
                ?? $currentEmployment?->years_in_business;
        }

        $documents = $loan->documents ?? collect();
        $trustScores = $documents->pluck('trust_score')->filter(fn ($v) => $v !== null);
        $avgTrustScore = $trustScores->isNotEmpty() ? (float) $trustScores->avg() : null;

        $docTypes = $documents->pluck('document_type')->filter()->values()->all();
        $hasDoc = fn (string $type): bool => in_array($type, $docTypes, true);

        $existingLoans = $primaryApplicant?->existingLoans ?? collect();
        $activeExistingLoans = $existingLoans->filter(fn ($l) => method_exists($l, 'isActive') ? $l->isActive() : false);

        $obligationMonthly = null;
        if ($loan->monthly_obligations !== null) {
            $obligationMonthly = (float) $loan->monthly_obligations;
        } elseif ($activeExistingLoans->isNotEmpty()) {
            $obligationMonthly = (float) $activeExistingLoans->sum(fn ($l) => (float) ($l->obligation_amount ?? 0));
        }

        $maxDpd = $existingLoans->pluck('dpd_days')->filter(fn ($v) => $v !== null)->max();
        $hasOverdue = (bool) $existingLoans->contains(fn ($l) => (bool) ($l->has_overdue ?? false));

        $creditScore = null;
        if ($loan->cibil_score !== null) {
            $creditScore = (int) $loan->cibil_score;
        } else {
            $latestCreditCheck = $loan->creditChecks
                ->sortByDesc(fn ($c) => $c->created_at)
                ->first();
            if ($latestCreditCheck?->credit_score !== null) {
                $creditScore = (int) $latestCreditCheck->credit_score;
            }
        }

        $dti = null;
        if ($loan->foir !== null) {
            $dti = (float) $loan->foir;
        } elseif ($incomeMonthly !== null && $incomeMonthly > 0 && $obligationMonthly !== null) {
            $dti = ($obligationMonthly / $incomeMonthly) * 100;
        }

        $facts = [
            'loan' => [
                'id' => (string) $loan->id,
                'application_number' => $loan->application_number,
                'loan_type' => $loan->loan_type,
                'requested_amount' => $loan->requested_amount !== null ? (float) $loan->requested_amount : null,
                'tenure_months' => $loan->tenure_months !== null ? (int) $loan->tenure_months : null,
                'requested_tenure_months' => $loan->requested_tenure_months !== null ? (int) $loan->requested_tenure_months : null,
                'status' => $loan->status,
                'is_high_value' => (bool) ($loan->is_high_value ?? false),
            ],
            'applicant' => [
                'age_years' => $ageYears,
                'employment_type' => $currentEmploymentType,
                'employment_years' => $employmentYears !== null ? (int) $employmentYears : null,
                'monthly_income' => $incomeMonthly,
            ],
            'credit' => [
                'cibil_score' => $creditScore,
                'credit_score' => $creditScore,
                'foir' => $dti,
                'dti' => $dti,
                'ltv_ratio' => $loan->ltv_ratio !== null ? (float) $loan->ltv_ratio : null,
                'risk_category' => $loan->risk_category,
                'risk_score' => $loan->risk_score !== null ? (int) $loan->risk_score : null,
                'monthly_obligations' => $obligationMonthly,
            ],
            'documents' => [
                'types' => $docTypes,
                'has_pan' => $hasDoc('pan'),
                'has_aadhaar' => $hasDoc('aadhaar'),
                'has_income_proof' => $hasDoc('income_proof'),
                'trust_score_avg' => $avgTrustScore,
            ],
            'existing_loans' => [
                'count' => (int) $existingLoans->count(),
                'active_count' => (int) $activeExistingLoans->count(),
                'max_dpd_days' => $maxDpd !== null ? (int) $maxDpd : null,
                'has_overdue' => $hasOverdue,
            ],
        ];

        // Convenience top-level aliases (helps keep rules simpler)
        $facts['cibil_score'] = Arr::get($facts, 'credit.cibil_score');
        $facts['credit_score'] = Arr::get($facts, 'credit.credit_score');
        $facts['foir'] = Arr::get($facts, 'credit.foir');
        $facts['dti'] = Arr::get($facts, 'credit.dti');
        $facts['ltv_ratio'] = Arr::get($facts, 'credit.ltv_ratio');
        $facts['monthly_income'] = Arr::get($facts, 'applicant.monthly_income');
        $facts['employment_years'] = Arr::get($facts, 'applicant.employment_years');
        $facts['trust_score_avg'] = Arr::get($facts, 'documents.trust_score_avg');

        return $facts;
    }
}
