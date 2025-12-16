<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditCard extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'applicant_id',
        'card_issuer',
        'card_type',
        'card_variant',
        'card_number_last_4',
        'card_holder_name',
        'credit_limit',
        'available_credit',
        'utilized_credit',
        'credit_utilization_percentage',
        'card_status',
        'card_issue_date',
        'card_expiry_date',
        'card_vintage_months',
        'average_monthly_spend',
        'current_outstanding',
        'is_payment_regular',
        'missed_payment_count',
        'last_payment_date',
        'is_verified',
        'verified_at',
        'verified_by',
        'verification_notes',
        'card_statement_path',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'available_credit' => 'decimal:2',
        'utilized_credit' => 'decimal:2',
        'credit_utilization_percentage' => 'decimal:2',
        'card_issue_date' => 'date',
        'card_expiry_date' => 'date',
        'card_vintage_months' => 'integer',
        'average_monthly_spend' => 'decimal:2',
        'current_outstanding' => 'decimal:2',
        'is_payment_regular' => 'boolean',
        'missed_payment_count' => 'integer',
        'last_payment_date' => 'date',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the applicant that owns the credit card.
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
     * Get masked card number.
     */
    public function getMaskedCardNumberAttribute(): string
    {
        return 'XXXX XXXX XXXX ' . $this->card_number_last_4;
    }

    /**
     * Check if card has good payment history.
     */
    public function hasGoodPaymentHistory(): bool
    {
        return $this->is_payment_regular && $this->missed_payment_count === 0;
    }

    /**
     * Check if utilization is healthy (< 30%).
     */
    public function hasHealthyUtilization(): bool
    {
        return $this->credit_utilization_percentage < 30;
    }

    /**
     * Scope for active cards.
     */
    public function scopeActive($query)
    {
        return $query->where('card_status', 'ACTIVE');
    }

    /**
     * Scope for verified cards.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
