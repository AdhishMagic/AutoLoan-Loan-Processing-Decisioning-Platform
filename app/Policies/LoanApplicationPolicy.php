<?php

namespace App\Policies;

use App\Models\LoanApplication;
use App\Models\User;

class LoanApplicationPolicy
{
    public function view(User $user, LoanApplication $loan): bool
    {
        // Always allow the owner to view their own application
        if ($loan->user_id === $user->id) {
            return true;
        }

        // Role-based access for staff
        return in_array($user->role?->name, ['admin', 'manager', 'customer_service'], true);
    }

    public function create(User $user): bool
    {
        return $user->isUser();
    }

    public function update(User $user, LoanApplication $loan): bool
    {
        // Owner can update while in draft; admins can always update
        return $user->isAdmin()
            || ($loan->user_id === $user->id && ($loan->isDraft() || $loan->status === 'DRAFT'));
    }

    public function approve(User $user, LoanApplication $loan): bool
    {
        // Align with controller which sets 'approved' and related statuses.
        // Allow approval when loan is submitted or under review (case-insensitive checks).
        $status = strtoupper((string) $loan->status);
        return $user->isLoanOfficer() && in_array($status, ['SUBMITTED', 'UNDER_REVIEW', 'PENDING_APPROVAL'], true);
    }

    public function reject(User $user, LoanApplication $loan): bool
    {
        return $this->approve($user, $loan);
    }

    public function hold(User $user, LoanApplication $loan): bool
    {
        return $this->approve($user, $loan);
    }

    public function delete(User $user, LoanApplication $loan): bool
    {
        // Admins can delete any application
        if ($user->isAdmin()) {
            return true;
        }

        // Owners can delete only while in DRAFT
        $status = strtoupper((string) $loan->status);
        return $loan->user_id === $user->id && $status === 'DRAFT';
    }
}
