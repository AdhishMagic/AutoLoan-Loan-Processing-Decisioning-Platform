<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'applicant_id',
        'account_type',
        'bank_name',
        'branch_name',
        'ifsc_code',
        'micr_code',
        'swift_code',
        'account_number',
        'account_holder_name',
        'account_opening_date',
        'account_vintage_months',
        'account_status',
        'average_monthly_balance',
        'current_balance',
        'minimum_balance',
        'monthly_credit_count',
        'monthly_credit_amount',
        'monthly_debit_count',
        'monthly_debit_amount',
        'bounced_cheque_count',
        'returned_emi_count',
        'has_overdraft_facility',
        'overdraft_limit',
        'is_salary_account',
        'is_primary_account',
        'is_loan_disbursement_account',
        'is_verified',
        'verified_at',
        'verified_by',
        'verification_method',
        'verification_notes',
        'bank_statement_path',
        'cancelled_cheque_path',
        'passbook_front_page_path',
    ];

    protected $casts = [
        'account_opening_date' => 'date',
        'account_vintage_months' => 'integer',
        'average_monthly_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'minimum_balance' => 'decimal:2',
        'monthly_credit_count' => 'integer',
        'monthly_credit_amount' => 'decimal:2',
        'monthly_debit_count' => 'integer',
        'monthly_debit_amount' => 'decimal:2',
        'bounced_cheque_count' => 'integer',
        'returned_emi_count' => 'integer',
        'has_overdraft_facility' => 'boolean',
        'overdraft_limit' => 'decimal:2',
        'is_salary_account' => 'boolean',
        'is_primary_account' => 'boolean',
        'is_loan_disbursement_account' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the applicant that owns the bank account.
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
     * Get income details using this account.
     */
    public function incomeDetails(): HasMany
    {
        return $this->hasMany(IncomeDetail::class, 'salary_bank_account_id');
    }

    /**
     * Get masked account number.
     */
    public function getMaskedAccountNumberAttribute(): string
    {
        if (strlen($this->account_number) <= 4) {
            return $this->account_number;
        }
        
        $last4 = substr($this->account_number, -4);
        $masked = str_repeat('X', strlen($this->account_number) - 4);
        
        return $masked . $last4;
    }

    /**
     * Check if account has good banking behavior.
     */
    public function hasGoodBankingBehavior(): bool
    {
        return $this->bounced_cheque_count === 0 && 
               $this->returned_emi_count === 0 &&
               $this->account_status === 'ACTIVE';
    }

    /**
     * Scope for active accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('account_status', 'ACTIVE');
    }

    /**
     * Scope for verified accounts.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for primary accounts.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary_account', true);
    }
}
