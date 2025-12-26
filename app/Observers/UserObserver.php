<?php

namespace App\Observers;

use App\Models\User;
use App\Services\LoanCacheService;

class UserObserver
{
    public function __construct(private readonly LoanCacheService $cache)
    {
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged(['name', 'email', 'phone', 'role_id', 'status'])) {
            $this->cache->forgetUserProfile((int) $user->id);
        }
    }
}
