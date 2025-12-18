<?php

namespace App\Support;

final class LoanStatus
{
    public const DRAFT = 'DRAFT';
    public const SUBMITTED = 'SUBMITTED';
    public const PROCESSING = 'PROCESSING';
    public const UNDER_REVIEW = 'UNDER_REVIEW';
    public const APPROVED = 'APPROVED';
    public const REJECTED = 'REJECTED';
    public const ON_HOLD = 'ON_HOLD';
}
