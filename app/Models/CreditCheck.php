<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CreditCheck extends Model
{
    use HasUuids;

    /**
     * The primary key is a UUID.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /** @var array<int, string> */
    protected $fillable = [
        'loan_application_id',
        'credit_score',
        'risk_level',
        'source',
    ];

    /**
     * @return BelongsTo<LoanApplication, CreditCheck>
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }
}
