<?php

namespace App\Policies;

use App\Models\LoanApplication;
use App\Models\User;

class LoanDocumentPolicy
{
    public function upload(User $user, LoanApplication $loan): bool
    {
        return $user->isUser() && $loan->user_id === $user->id;
    }

    public function verify(User $user): bool
    {
        return $user->isCustomerSupport();
    }
}
