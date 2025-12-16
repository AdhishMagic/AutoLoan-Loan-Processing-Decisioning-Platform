<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'address_type',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'landmark',
        'locality',
        'city',
        'district',
        'state',
        'country',
        'pincode',
        'is_verified',
        'verified_at',
        'verified_by',
        'verification_notes',
        'proof_type',
        'proof_document_path',
        'latitude',
        'longitude',
        'years_at_address',
        'months_at_address',
        'is_primary',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'years_at_address' => 'integer',
        'months_at_address' => 'integer',
        'is_primary' => 'boolean',
    ];

    /**
     * Get the parent addressable model (Applicant, Property, etc.).
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the verifier user.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get full address as a single string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->address_line_3,
            $this->landmark,
            $this->locality,
            $this->city,
            $this->district,
            $this->state,
            $this->country,
            $this->pincode,
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Scope for verified addresses.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for primary addresses.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
