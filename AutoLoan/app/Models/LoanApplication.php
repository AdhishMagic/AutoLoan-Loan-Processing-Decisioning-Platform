<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanApplication extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'loan_type',
        'requested_amount',
        'tenure_months',
        'interest_rate',
        'status',
        'assigned_officer_id',
        'submitted_at',
        'approved_at',
        'rejected_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'requested_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, LoanApplication>
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<User, LoanApplication>
     */
    public function assignedOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_officer_id');
    }

    /**
     * @return HasMany<LoanDocument>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(LoanDocument::class);
    }

    /**
     * @return HasMany<KycCheck>
     */
    public function kycChecks(): HasMany
    {
        return $this->hasMany(KycCheck::class);
    }

    /**
     * @return HasMany<CreditCheck>
     */
    public function creditChecks(): HasMany
    {
        return $this->hasMany(CreditCheck::class);
    }

    /**
     * @return HasMany<LoanDecision>
     */
    public function decisions(): HasMany
    {
        return $this->hasMany(LoanDecision::class);
    }
}
