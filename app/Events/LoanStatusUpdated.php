<?php

namespace App\Events;

use App\Models\LoanApplication;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoanStatusUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public LoanApplication $loan,
        public string $oldStatus,
        public string $newStatus,
    ) {
    }
}
