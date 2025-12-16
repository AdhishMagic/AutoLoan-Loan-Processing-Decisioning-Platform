<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanApplication extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'application_number',
        'application_date',
        'user_id',
        'loan_product_type',
        'loan_product_code',
        'loan_scheme',
        'requested_amount',
        'sanctioned_amount',
        'disbursed_amount',
        'requested_tenure_months',
        'sanctioned_tenure_months',
        'requested_interest_rate',
        'sanctioned_interest_rate',
        'interest_type',
        'processing_fee',
        'processing_fee_percentage',
        'emi_amount',
        'emi_date',
        'loan_purpose',
        'purpose_description',
        'status',
        'current_stage',
        'stage_order',
        'assigned_officer_id',
        'credit_manager_id',
        'underwriter_id',
        'assigned_branch',
        'submitted_at',
        'kyc_completed_at',
        'documents_completed_at',
        'credit_check_completed_at',
        'technical_verification_at',
        'legal_verification_at',
        'valuation_completed_at',
        'approved_at',
        'rejected_at',
        'sanctioned_at',
        'disbursed_at',
        'rejection_reason',
        'cancellation_reason',
        'rejected_by',
        'approved_by',
        'monthly_income',
        'monthly_obligations',
        'foir',
        'ltv_ratio',
        'dscr',
        'cibil_score',
        'credit_bureau',
        'credit_report_date',
        'risk_category',
        'risk_score',
        'loan_account_number',
        'disbursement_bank_account_id',
        'disbursement_mode',
        'disbursement_reference',
        'disbursement_remarks',
        'preferred_communication',
        'priority',
        'sla_deadline',
        'is_sla_breached',
        'is_high_value',
        'requires_manager_approval',
        'is_fast_track',
        'is_top_up_loan',
        'parent_loan_id',
        'internal_notes',
        'customer_notes',
    ];

    protected $casts = [
        'application_date' => 'date',
        'requested_amount' => 'decimal:2',
        'sanctioned_amount' => 'decimal:2',
        'disbursed_amount' => 'decimal:2',
        'requested_tenure_months' => 'integer',
        'sanctioned_tenure_months' => 'integer',
        'requested_interest_rate' => 'decimal:2',
        'sanctioned_interest_rate' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'processing_fee_percentage' => 'decimal:2',
        'emi_amount' => 'decimal:2',
        'emi_date' => 'integer',
        'stage_order' => 'integer',
        'submitted_at' => 'datetime',
        'kyc_completed_at' => 'datetime',
        'documents_completed_at' => 'datetime',
        'credit_check_completed_at' => 'datetime',
        'technical_verification_at' => 'datetime',
        'legal_verification_at' => 'datetime',
        'valuation_completed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'sanctioned_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'monthly_income' => 'decimal:2',
        'monthly_obligations' => 'decimal:2',
        'foir' => 'decimal:2',
        'ltv_ratio' => 'decimal:2',
        'dscr' => 'decimal:2',
        'cibil_score' => 'integer',
        'credit_report_date' => 'date',
        'risk_score' => 'integer',
        'sla_deadline' => 'datetime',
        'is_sla_breached' => 'boolean',
        'is_high_value' => 'boolean',
        'requires_manager_approval' => 'boolean',
        'is_fast_track' => 'boolean',
        'is_top_up_loan' => 'boolean',
    ];

    // Relationships to Users
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_officer_id');
    }

    public function creditManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'credit_manager_id');
    }

    public function underwriter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'underwriter_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Applicants
    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }

    public function primaryApplicant(): HasMany
    {
        return $this->applicants()->where('applicant_role', 'PRIMARY');
    }

    public function coApplicants(): HasMany
    {
        return $this->applicants()->where('applicant_role', 'CO_APPLICANT');
    }

    // Properties
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function property(): HasMany
    {
        return $this->properties()->limit(1);
    }

    // References
    public function references(): HasMany
    {
        return $this->hasMany(LoanReference::class);
    }

    // Declarations
    public function declarations(): HasMany
    {
        return $this->hasMany(LoanDeclaration::class);
    }

    public function mandatoryDeclarations(): HasMany
    {
        return $this->declarations()->where('is_mandatory', true);
    }

    // History & Audit
    public function statusHistory(): HasMany
    {
        return $this->hasMany(LoanStatusHistory::class)->orderBy('action_timestamp', 'desc');
    }

    // Existing relationships
    public function documents(): HasMany
    {
        return $this->hasMany(LoanDocument::class);
    }

    public function kycChecks(): HasMany
    {
        return $this->hasMany(KycCheck::class);
    }

    public function creditChecks(): HasMany
    {
        return $this->hasMany(CreditCheck::class);
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(LoanDecision::class);
    }

    // Parent/Child for top-up loans
    public function parentLoan(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'parent_loan_id');
    }

    public function topUpLoans(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'parent_loan_id');
    }

    // Helper Methods
    public function isDraft(): bool
    {
        return $this->status === 'DRAFT';
    }

    public function isSubmitted(): bool
    {
        return !$this->isDraft() && !is_null($this->submitted_at);
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function isRejected(): bool
    {
        return $this->status === 'REJECTED';
    }

    public function isDisbursed(): bool
    {
        return $this->status === 'DISBURSED';
    }

    public function isSlaBreached(): bool
    {
        return $this->is_sla_breached;
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_officer_id', $userId);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['HIGH', 'URGENT']);
    }

    public function scopeSlaBreached($query)
    {
        return $query->where('is_sla_breached', true);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'PENDING_APPROVAL');
    }
}
