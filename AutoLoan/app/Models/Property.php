<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'loan_application_id',
        'property_type',
        'property_sub_type',
        'construction_status',
        'property_age_years',
        'ownership_type',
        'owner_name',
        'co_owners',
        'property_id',
        'survey_number',
        'plot_number',
        'khata_number',
        'deed_number',
        'registration_number',
        'carpet_area_sqft',
        'built_up_area_sqft',
        'super_built_up_area_sqft',
        'plot_area_sqft',
        'market_value',
        'government_value',
        'agreement_value',
        'stamp_duty_value',
        'rate_per_sqft',
        'valuation_date',
        'valuation_report_number',
        'valued_by',
        'builder_name',
        'project_name',
        'wing_tower',
        'floor_number',
        'flat_unit_number',
        'parking_count',
        'parking_type',
        'property_approval_status',
        'has_clear_title',
        'has_encumbrance',
        'is_mortgaged',
        'mortgaged_to',
        'amenities',
        'boundary_north',
        'boundary_south',
        'boundary_east',
        'boundary_west',
        'maintenance_charges',
        'property_tax_annual',
        'society_charges',
        'is_insured',
        'insurance_company',
        'insurance_policy_number',
        'insurance_amount',
        'insurance_expiry_date',
        'verification_status',
        'technical_verified_at',
        'technical_verified_by',
        'legal_verified_at',
        'legal_verified_by',
        'verification_notes',
        'sale_deed_path',
        'title_deed_path',
        'ec_path',
        'tax_receipt_path',
        'approved_plan_path',
        'noc_path',
        'valuation_report_path',
    ];

    protected $casts = [
        'property_age_years' => 'integer',
        'co_owners' => 'array',
        'carpet_area_sqft' => 'decimal:2',
        'built_up_area_sqft' => 'decimal:2',
        'super_built_up_area_sqft' => 'decimal:2',
        'plot_area_sqft' => 'decimal:2',
        'market_value' => 'decimal:2',
        'government_value' => 'decimal:2',
        'agreement_value' => 'decimal:2',
        'stamp_duty_value' => 'decimal:2',
        'rate_per_sqft' => 'decimal:2',
        'valuation_date' => 'date',
        'parking_count' => 'integer',
        'has_clear_title' => 'boolean',
        'has_encumbrance' => 'boolean',
        'is_mortgaged' => 'boolean',
        'amenities' => 'array',
        'maintenance_charges' => 'decimal:2',
        'property_tax_annual' => 'decimal:2',
        'society_charges' => 'decimal:2',
        'is_insured' => 'boolean',
        'insurance_amount' => 'decimal:2',
        'insurance_expiry_date' => 'date',
        'technical_verified_at' => 'datetime',
        'legal_verified_at' => 'datetime',
    ];

    /**
     * Get the loan application that owns the property.
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    /**
     * Get the valuer user.
     */
    public function valuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valued_by');
    }

    /**
     * Get the technical verifier.
     */
    public function technicalVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technical_verified_by');
    }

    /**
     * Get the legal verifier.
     */
    public function legalVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'legal_verified_by');
    }

    /**
     * Get property addresses.
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Check if property is fully verified.
     */
    public function isFullyVerified(): bool
    {
        return $this->verification_status === 'FULLY_VERIFIED';
    }

    /**
     * Check if property has clear title.
     */
    public function hasClearTitle(): bool
    {
        return $this->has_clear_title && 
               !$this->has_encumbrance &&
               $this->property_approval_status === 'CLEAR';
    }

    /**
     * Get total area for calculation.
     */
    public function getTotalAreaAttribute(): float
    {
        return $this->super_built_up_area_sqft 
            ?? $this->built_up_area_sqft 
            ?? $this->carpet_area_sqft 
            ?? $this->plot_area_sqft 
            ?? 0;
    }

    /**
     * Scope for verified properties.
     */
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'FULLY_VERIFIED');
    }

    /**
     * Scope for clear title properties.
     */
    public function scopeClearTitle($query)
    {
        return $query->where('has_clear_title', true)
                    ->where('has_encumbrance', false);
    }
}
