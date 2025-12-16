<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applicant extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'loan_application_id',
        'user_id',
        'applicant_role',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'marital_status',
        'father_name',
        'mother_name',
        'spouse_name',
        'mobile',
        'alternate_mobile',
        'email',
        'alternate_email',
        'pan_number',
        'aadhaar_number',
        'passport_number',
        'voter_id',
        'driving_license',
        'religion',
        'category',
        'nationality',
        'education_level',
        'residential_status',
        'years_at_current_residence',
        'number_of_dependents',
        'kyc_status',
        'kyc_reference_number',
        'kyc_verified_at',
        'kyc_verified_by',
        'is_politically_exposed',
        'additional_notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'kyc_verified_at' => 'datetime',
        'is_politically_exposed' => 'boolean',
        'number_of_dependents' => 'integer',
        'years_at_current_residence' => 'integer',
    ];

    /**
     * Get the loan application that owns the applicant.
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    /**
     * Get the user associated with the applicant.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the KYC verifier.
     */
    public function kycVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kyc_verified_by');
    }

    /**
     * Get all addresses for the applicant.
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Get the current address.
     */
    public function currentAddress(): MorphMany
    {
        return $this->addresses()->where('address_type', 'CURRENT');
    }

    /**
     * Get the permanent address.
     */
    public function permanentAddress(): MorphMany
    {
        return $this->addresses()->where('address_type', 'PERMANENT');
    }

    /**
     * Get all employment details for the applicant.
     */
    public function employmentDetails(): HasMany
    {
        return $this->hasMany(EmploymentDetail::class);
    }

    /**
     * Get current employment.
     */
    public function currentEmployment(): HasMany
    {
        return $this->employmentDetails()->where('employment_status', 'CURRENT');
    }

    /**
     * Get all income details for the applicant.
     */
    public function incomeDetails(): HasMany
    {
        return $this->hasMany(IncomeDetail::class);
    }

    /**
     * Get all bank accounts for the applicant.
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    /**
     * Get primary bank account.
     */
    public function primaryBankAccount(): HasMany
    {
        return $this->bankAccounts()->where('is_primary_account', true);
    }

    /**
     * Get all credit cards for the applicant.
     */
    public function creditCards(): HasMany
    {
        return $this->hasMany(CreditCard::class);
    }

    /**
     * Get all existing loans for the applicant.
     */
    public function existingLoans(): HasMany
    {
        return $this->hasMany(ExistingLoan::class);
    }

    /**
     * Get active existing loans.
     */
    public function activeLoans(): HasMany
    {
        return $this->existingLoans()->where('repayment_status', 'REGULAR');
    }

    /**
     * Get all declarations for the applicant.
     */
    public function declarations(): HasMany
    {
        return $this->hasMany(LoanDeclaration::class);
    }

    /**
     * Get the applicant's full name.
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);
        
        return implode(' ', $parts);
    }

    /**
     * Check if applicant is primary.
     */
    public function isPrimary(): bool
    {
        return $this->applicant_role === 'PRIMARY';
    }

    /**
     * Check if applicant is co-applicant.
     */
    public function isCoApplicant(): bool
    {
        return $this->applicant_role === 'CO_APPLICANT';
    }

    /**
     * Check if KYC is verified.
     */
    public function isKycVerified(): bool
    {
        return $this->kyc_status === 'VERIFIED';
    }
}
