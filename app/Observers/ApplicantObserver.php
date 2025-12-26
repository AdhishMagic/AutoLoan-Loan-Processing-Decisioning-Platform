<?php

namespace App\Observers;

use App\Models\Applicant;
use App\Services\LoanCacheService;

class ApplicantObserver
{
    public function __construct(private readonly LoanCacheService $cache)
    {
    }

    public function updated(Applicant $applicant): void
    {
        if ($applicant->wasChanged(['kyc_status', 'kyc_reference_number', 'kyc_verified_at', 'kyc_verified_by'])) {
            $this->cache->forgetKycResult((string) $applicant->loan_application_id);
        }
    }
}
