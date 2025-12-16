<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanReference extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'loan_application_id',
        'reference_type',
        'full_name',
        'relationship',
        'mobile',
        'alternate_mobile',
        'email',
        'address',
        'city',
        'state',
        'pincode',
        'occupation',
        'company_name',
        'designation',
        'known_since_years',
        'how_do_you_know',
        'verification_status',
        'contacted_at',
        'verified_at',
        'verified_by',
        'verification_method',
        'verification_notes',
        'reference_feedback',
        'feedback_rating',
        'priority_order',
    ];

    protected $casts = [
        'known_since_years' => 'integer',
        'contacted_at' => 'datetime',
        'verified_at' => 'datetime',
        'priority_order' => 'integer',
    ];

    /**
     * Get the loan application that owns the reference.
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    /**
     * Get the verifier user.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if reference is verified.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'VERIFIED';
    }

    /**
     * Check if reference has positive feedback.
     */
    public function hasPositiveFeedback(): bool
    {
        return $this->feedback_rating === 'POSITIVE';
    }

    /**
     * Scope for verified references.
     */
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'VERIFIED');
    }

    /**
     * Scope for pending references.
     */
    public function scopePending($query)
    {
        return $query->where('verification_status', 'PENDING');
    }

    /**
     * Scope ordered by priority.
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority_order');
    }
}
