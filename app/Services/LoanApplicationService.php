<?php

namespace App\Services;

use App\DTOs\LoanDto;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LoanApplicationService
{
    public function submit(LoanDto $dto): LoanApplication
    {
        return DB::transaction(function () use ($dto) {
            $aadhaarPath = $dto->aadhaar->store('loans/aadhaar');
            $panPath = $dto->pan->store('loans/pan');

            $loan = LoanApplication::create([
                'user_id' => $dto->userId,
                'loan_product_type' => $dto->loanProductType,
                'loan_amount' => $dto->loanAmount,
                'tenure_months' => $dto->tenureMonths,
                'monthly_income' => $dto->monthlyIncome,
                'employment_type' => $dto->employmentType,
                'aadhaar_path' => $aadhaarPath,
                'pan_path' => $panPath,
                'status' => 'submitted',
            ]);

            return $loan;
        });
    }
}
