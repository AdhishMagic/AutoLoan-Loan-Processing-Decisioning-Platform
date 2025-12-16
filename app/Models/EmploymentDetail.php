<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmploymentDetail extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'applicant_id',
        'employment_type',
        'employment_status',
        'company_name',
        'company_type',
        'industry_type',
        'industry_code',
        'company_pan',
        'company_gstin',
        'company_incorporation_date',
        'designation',
        'department',
        'employee_id',
        'date_of_joining',
        'date_of_leaving',
        'total_experience_years',
        'total_experience_months',
        'current_company_experience_years',
        'current_company_experience_months',
        'business_nature',
        'years_in_business',
        'office_ownership',
        'office_phone',
        'office_email',
        'reporting_manager_name',
        'reporting_manager_contact',
        'hr_contact_name',
        'hr_contact_phone',
        'is_verified',
        'verified_at',
        'verified_by',
        'verification_method',
        'verification_notes',
        'appointment_letter_path',
        'experience_letter_path',
        'business_registration_path',
    ];

    protected $casts = [
        'company_incorporation_date' => 'date',
        'date_of_joining' => 'date',
        'date_of_leaving' => 'date',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'total_experience_years' => 'integer',
        'total_experience_months' => 'integer',
        'current_company_experience_years' => 'integer',
        'current_company_experience_months' => 'integer',
        'years_in_business' => 'integer',
    ];

    /**
     * Get the applicant that owns the employment detail.
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
     * Get office addresses.
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Check if employment is current.
     */
    public function isCurrent(): bool
    {
        return $this->employment_status === 'CURRENT';
    }

    /**
     * Check if salaried.
     */
    public function isSalaried(): bool
    {
        return $this->employment_type === 'SALARIED';
    }

    /**
     * Check if self-employed.
     */
    public function isSelfEmployed(): bool
    {
        return in_array($this->employment_type, [
            'SELF_EMPLOYED_PROFESSIONAL',
            'SELF_EMPLOYED_BUSINESS'
        ]);
    }

    /**
     * Scope for current employment.
     */
    public function scopeCurrent($query)
    {
        return $query->where('employment_status', 'CURRENT');
    }

    /**
     * Scope for verified employment.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
