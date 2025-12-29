<?php

namespace App\DTOs;

use App\Http\Requests\StoreLoanRequest;
use Illuminate\Http\UploadedFile;

final readonly class LoanDto
{
    public function __construct(
        public string $loanProductType,
        public int $loanAmount,
        public int $tenureMonths,
        public int $monthlyIncome,
        public string $employmentType,
        public UploadedFile $aadhaar,
        public UploadedFile $pan,
        public int $userId,
    ) {}

    public static function fromRequest(StoreLoanRequest $request): self
    {
        return new self(
            loanProductType: $request->input('loan_product_type'),
            loanAmount: (int) $request->input('loan_amount'),
            tenureMonths: (int) $request->input('tenure_months'),
            monthlyIncome: (int) $request->input('monthly_income'),
            employmentType: $request->input('employment_type'),
            aadhaar: $request->file('aadhaar'),
            pan: $request->file('pan'),
            userId: $request->user()->id,
        );
    }
}
