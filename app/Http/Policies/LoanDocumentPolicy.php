<?php

namespace App\Http\Policies;

use App\Models\LoanApplication;
use App\Models\LoanDocument;
use App\Models\User;

class LoanDocumentPolicy
{
    /**
     * Controls upload / replace for a specific loan.
     */
    public function create(User $user, LoanApplication $loan): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isLoanOfficer()) {
            return (string) $loan->assigned_officer_id === (string) $user->id;
        }

        return $user->isUser() && (string) $loan->user_id === (string) $user->id;
    }

    /**
     * Controls access for viewing/downloading a specific document.
     */
    public function view(User $user, LoanDocument $document): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $loan = $document->loanApplication;
        if (! $loan) {
            return false;
        }

        if ($user->isLoanOfficer()) {
            return (string) $loan->assigned_officer_id === (string) $user->id;
        }

        return $user->isUser() && (string) $loan->user_id === (string) $user->id;
    }
}
