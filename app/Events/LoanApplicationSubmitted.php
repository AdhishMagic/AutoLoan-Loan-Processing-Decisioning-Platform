<?php

namespace App\Events;

use App\Models\LoanApplication;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoanApplicationSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(public LoanApplication $loan)
    {
    }
}
