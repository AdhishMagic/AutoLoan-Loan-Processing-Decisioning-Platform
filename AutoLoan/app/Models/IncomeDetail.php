<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomeDetail extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'applicant_id',
        'income_type',
        'income_frequency',
        'gross_income_amount',
        'net_income_amount',
        'deductions_amount',
        'gross_annual_income',
        'net_annual_income',
        'basic_salary',
        'hra',
        'special_allowance',
        'variable_pay',
        'bonus',
        'commission',
        'overtime',
        'other_allowances',
        'pf_deduction',
        'professional_tax',
        'tds',
        'esi',
        'loan_deduction',
        'other_deductions',
        'turnover',
        'net_profit',
        'depreciation',
        'salary_mode',
        'salary_bank_account_id',
        'itr_filing_status',
        'last_itr_year',
        'last_itr_income',
        'salary_slip_path',
        'form16_path',
        'itr_path',
        'bank_statement_path',
        'audit_report_path',
        'is_verified',
        'verified_at',
        'verified_by',
        'verification_notes',
        'reference_month',
        'reference_year',
    ];

    protected $casts = [
        'gross_income_amount' => 'decimal:2',
        'net_income_amount' => 'decimal:2',
        'deductions_amount' => 'decimal:2',
        'gross_annual_income' => 'decimal:2',
        'net_annual_income' => 'decimal:2',
        'basic_salary' => 'decimal:2',
        'hra' => 'decimal:2',
        'special_allowance' => 'decimal:2',
        'variable_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
        'commission' => 'decimal:2',
        'overtime' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'pf_deduction' => 'decimal:2',
        'professional_tax' => 'decimal:2',
        'tds' => 'decimal:2',
        'esi' => 'decimal:2',
        'loan_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'turnover' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'depreciation' => 'decimal:2',
        'last_itr_income' => 'decimal:2',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the applicant that owns the income detail.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Get the bank account used for salary.
     */
    public function salaryBankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'salary_bank_account_id');
    }

    /**
     * Get the verifier user.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Calculate annual income from monthly.
     */
    public function getCalculatedAnnualIncomeAttribute(): float
    {
        if ($this->income_frequency === 'MONTHLY') {
            return $this->net_income_amount * 12;
        } elseif ($this->income_frequency === 'QUARTERLY') {
            return $this->net_income_amount * 4;
        }
        return $this->net_income_amount;
    }

    /**
     * Check if salary income.
     */
    public function isSalary(): bool
    {
        return $this->income_type === 'SALARY';
    }

    /**
     * Scope for verified income.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
