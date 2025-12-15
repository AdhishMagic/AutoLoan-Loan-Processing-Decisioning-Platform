<?php

namespace App\Policies;

use App\Models\LoanApplication;
use App\Models\User;

class LoanApplicationPolicy
{
    public function view(User $user, LoanApplication $loan): bool
    {
        return match ($user->role?->name) {
            'admin' => true,
            'manager' => true,
            'customer_service' => true,
            'user' => $loan->user_id === $user->id,
            default => false,
        };
    }

    public function create(User $user): bool
    {
        return $user->isUser();
    }

    public function update(User $user, LoanApplication $loan): bool
    {
        return $user->isAdmin()
            || ($user->isUser() && $loan->user_id === $user->id && $loan->status === 'draft');
    }

    public function approve(User $user, LoanApplication $loan): bool
    {
        return $user->isLoanOfficer() && $loan->status === 'under_review';
    }

    public function reject(User $user, LoanApplication $loan): bool
    {
        return $this->approve($user, $loan);
    }

    public function hold(User $user, LoanApplication $loan): bool
    {
        return $this->approve($user, $loan);
    }
}
