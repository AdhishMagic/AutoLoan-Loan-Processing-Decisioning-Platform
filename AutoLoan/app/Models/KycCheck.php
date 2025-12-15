<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycCheck extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'loan_application_id',
        'kyc_type',
        'result',
        'verified_by',
    ];

    /**
     * @return BelongsTo<LoanApplication, KycCheck>
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * @return BelongsTo<User, KycCheck>
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
