<?php

namespace App\Services;

use App\Models\LoanApplication;
use App\Models\User;

class LoanApplicationService
{
    public function create(User $owner, array $data): LoanApplication
    {
        return LoanApplication::create([
            'user_id' => $owner->id,
            'loan_type' => $data['loan_type'],
            'requested_amount' => $data['requested_amount'],
            'tenure_months' => $data['tenure_months'],
            'status' => 'draft',
        ]);
    }

    public function update(LoanApplication $loan, array $data): LoanApplication
    {
        $loan->update([
            'loan_type' => $data['loan_type'],
            'requested_amount' => $data['requested_amount'],
            'tenure_months' => $data['tenure_months'],
        ]);

        return $loan;
    }

    public function delete(LoanApplication $loan): void
    {
        $loan->delete();
    }
}
