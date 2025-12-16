<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExistingLoan extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'applicant_id',
        'loan_type',
        'lender_name',
        'lender_type',
        'loan_account_number',
        'original_loan_amount',
        'current_outstanding',
        'emi_amount',
        'total_tenure_months',
        'remaining_tenure_months',
        'interest_rate',
        'interest_type',
        'loan_disbursement_date',
        'loan_maturity_date',
        'last_emi_date',
        'next_emi_date',
        'repayment_status',
        'dpd_days',
        'bounced_emi_count',
        'missed_emi_count',
        'has_overdue',
        'loan_security_type',
        'collateral_description',
        'is_to_be_closed',
        'preclosure_amount',
        'is_considered_for_obligation',
        'is_verified',
        'verified_at',
        'verified_by',
        'verification_method',
        'verification_notes',
        'loan_statement_path',
        'sanction_letter_path',
        'noc_path',
    ];

    protected $casts = [
        'original_loan_amount' => 'decimal:2',
        'current_outstanding' => 'decimal:2',
        'emi_amount' => 'decimal:2',
        'total_tenure_months' => 'integer',
        'remaining_tenure_months' => 'integer',
        'interest_rate' => 'decimal:2',
        'loan_disbursement_date' => 'date',
        'loan_maturity_date' => 'date',
        'last_emi_date' => 'date',
        'next_emi_date' => 'date',
        'dpd_days' => 'integer',
        'bounced_emi_count' => 'integer',
        'missed_emi_count' => 'integer',
        'has_overdue' => 'boolean',
        'is_to_be_closed' => 'boolean',
        'preclosure_amount' => 'decimal:2',
        'is_considered_for_obligation' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the applicant that owns the existing loan.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Get the verifier user.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if loan has good repayment history.
     */
    public function hasGoodRepaymentHistory(): bool
    {
        return $this->repayment_status === 'REGULAR' && 
               $this->dpd_days === 0 &&
               $this->bounced_emi_count === 0 &&
               $this->missed_emi_count === 0;
    }

    /**
     * Check if loan is active.
     */
    public function isActive(): bool
    {
        return in_array($this->repayment_status, ['REGULAR', 'IRREGULAR']);
    }

    /**
     * Get obligation amount (monthly EMI if considered).
     */
    public function getObligationAmountAttribute(): float
    {
        return $this->is_considered_for_obligation && !$this->is_to_be_closed 
            ? $this->emi_amount 
            : 0;
    }

    /**
     * Scope for active loans.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('repayment_status', ['REGULAR', 'IRREGULAR']);
    }

    /**
     * Scope for loans with good repayment.
     */
    public function scopeGoodRepayment($query)
    {
        return $query->where('repayment_status', 'REGULAR')
                    ->where('dpd_days', 0);
    }

    /**
     * Scope for considered obligations.
     */
    public function scopeConsideredObligations($query)
    {
        return $query->where('is_considered_for_obligation', true)
                    ->where('is_to_be_closed', false);
    }
}
