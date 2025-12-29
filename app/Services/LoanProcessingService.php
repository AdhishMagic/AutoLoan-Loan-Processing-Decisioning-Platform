<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\LoanDto;
use App\Models\LoanApplication;
use App\Models\LoanDocument;
use App\Repositories\Contracts\CreditRepositoryInterface;
use App\Repositories\Contracts\KycRepositoryInterface;
use App\Repositories\Contracts\LoanRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Application-layer orchestration service for loan submission and decisioning.
 *
 * Example flow:
 * 1) $dto = LoanDto::fromRequest($request)
 * 2) $payload = $loanProcessingService->submit($dto)
 */
final class LoanProcessingService
{
    public function __construct(
        private readonly LoanRepositoryInterface $loans,
        private readonly KycRepositoryInterface $kyc,
        private readonly CreditRepositoryInterface $credit,
        private readonly UnderwritingService $underwriting,
    ) {
    }

    /**
     * @return array{
     *   loan_id: string,
     *   application_number: string,
     *   status: string,
     *   decision: string,
     *   credit_score: int,
     *   dti: float
     * }
     */
    public function submit(LoanDto $dto): array
    {
        return DB::transaction(function () use ($dto): array {
            $applicationNumber = 'LAP-' . Str::upper(Str::random(12));

            $loan = $this->loans->create([
                'user_id' => $dto->userId,
                'application_number' => $applicationNumber,
                'application_date' => now()->toDateString(),
                'loan_type' => $dto->loanProductType,
                'loan_product_type' => $dto->loanProductType,
                'requested_amount' => $dto->loanAmount,
                'tenure_months' => $dto->tenureMonths,
                'requested_tenure_months' => $dto->tenureMonths,
                'monthly_income' => $dto->monthlyIncome,
                'status' => 'SUBMITTED',
                'submitted_at' => now(),
                'customer_notes' => '[employment_type] ' . $dto->employmentType,
            ]);

            $this->storeKycDocuments($loan, $dto);

            if (! $this->kyc->isVerified((string) $loan->id)) {
                $this->kyc->createResult(
                    loanId: (string) $loan->id,
                    kycType: 'document',
                    result: 'verified',
                    verifiedBy: null,
                );
            }

            $creditScore = $this->credit->latestScoreForLoan((string) $loan->id);
            if ($creditScore === null) {
                $creditScore = $this->simulateCreditScore($dto->monthlyIncome);
                $this->credit->storeResult(
                    loanId: (string) $loan->id,
                    creditScore: $creditScore,
                    riskLevel: $this->riskLevelForScore($creditScore),
                    source: 'internal_v1',
                );
            }

            $dti = $this->estimateDti(
                principal: (float) $dto->loanAmount,
                tenureMonths: $dto->tenureMonths,
                monthlyIncome: (float) $dto->monthlyIncome,
            );

            $decision = $this->underwriting->decide(
                creditScore: $creditScore,
                monthlyIncome: (float) $dto->monthlyIncome,
                dti: $dti,
            );

            $status = match ($decision) {
                UnderwritingService::DECISION_APPROVED => 'APPROVED',
                UnderwritingService::DECISION_REJECTED => 'REJECTED',
                default => 'UNDER_REVIEW',
            };

            $attributes = [];
            if ($status === 'APPROVED') {
                $attributes['approved_at'] = now();
            }
            if ($status === 'REJECTED') {
                $attributes['rejected_at'] = now();
                $attributes['rejection_reason'] = 'Automated underwriting rules';
            }
            $attributes['credit_check_completed_at'] = now();
            $attributes['kyc_completed_at'] = now();

            $this->loans->updateStatus($loan, $status, $attributes);

            return [
                'loan_id' => (string) $loan->id,
                'application_number' => $applicationNumber,
                'status' => (string) $loan->status,
                'decision' => $decision,
                'credit_score' => $creditScore,
                'dti' => $dti,
            ];
        });
    }

    public function getLoan(string $loanId): LoanApplication
    {
        $loan = $this->loans->findById($loanId);
        abort_unless($loan instanceof LoanApplication, 404, 'Loan not found');

        return $loan;
    }

    /**
     * @return array{loan_id: string, kyc_status: string|null, kyc_reference_number: string|null, kyc_verified_at: string|null}
     */
    public function getKycSummary(string $loanId): array
    {
        $latest = $this->kyc->latestForLoan($loanId);

        return [
            'loan_id' => $loanId,
            'kyc_status' => $latest?->result,
            'kyc_reference_number' => null,
            'kyc_verified_at' => null,
        ];
    }

    public function findDocumentForDownload(string $loanId, string $originalName): LoanDocument
    {
        $loan = $this->getLoan($loanId);
        $document = $this->loans->findDocumentByOriginalName($loan, $originalName);
        abort_unless($document instanceof LoanDocument, 404, 'Document not found');

        return $document;
    }

    private function storeKycDocuments(LoanApplication $loan, LoanDto $dto): void
    {
        $baseDir = 'loans/' . (string) $loan->id . '/kyc';

        $aadhaarPath = $dto->aadhaar->store($baseDir);
        $this->loans->createDocument(
            loan: $loan,
            userId: $dto->userId,
            documentType: LoanDocument::TYPE_AADHAAR,
            filePath: $aadhaarPath,
            originalName: $dto->aadhaar->getClientOriginalName(),
        );

        $panPath = $dto->pan->store($baseDir);
        $this->loans->createDocument(
            loan: $loan,
            userId: $dto->userId,
            documentType: LoanDocument::TYPE_PAN,
            filePath: $panPath,
            originalName: $dto->pan->getClientOriginalName(),
        );
    }

    private function simulateCreditScore(int $monthlyIncome): int
    {
        return match (true) {
            $monthlyIncome >= 100000 => 780,
            $monthlyIncome >= 60000 => 720,
            $monthlyIncome >= 30000 => 660,
            default => 580,
        };
    }

    private function riskLevelForScore(int $creditScore): string
    {
        return match (true) {
            $creditScore >= 750 => 'low',
            $creditScore >= 650 => 'medium',
            default => 'high',
        };
    }

    private function estimateDti(float $principal, int $tenureMonths, float $monthlyIncome): float
    {
        if ($monthlyIncome <= 0.0) {
            return 1.0;
        }

        $annualRate = 0.16;
        $monthlyRate = $annualRate / 12.0;

        if ($tenureMonths <= 0) {
            return 1.0;
        }

        if ($monthlyRate <= 0.0) {
            $emi = $principal / (float) $tenureMonths;
        } else {
            $factor = (1.0 + $monthlyRate) ** $tenureMonths;
            $emi = ($principal * $monthlyRate * $factor) / ($factor - 1.0);
        }

        $dti = $emi / $monthlyIncome;

        return max(0.0, min(1.0, $dti));
    }
}
