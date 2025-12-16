<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanDecision extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'loan_application_id',
        'decision',
        'remarks',
        'decided_by',
    ];

    /**
     * @return BelongsTo<LoanApplication, LoanDecision>
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * @return BelongsTo<User, LoanDecision>
     */
    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
