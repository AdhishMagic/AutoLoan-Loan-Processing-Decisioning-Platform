<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanDeclaration extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'loan_application_id',
        'applicant_id',
        'declaration_type',
        'declaration_title',
        'declaration_text',
        'declaration_points',
        'is_accepted',
        'accepted_at',
        'accepted_ip',
        'accepted_user_agent',
        'accepted_device_info',
        'digital_signature_hash',
        'signature_image_path',
        'declaration_version',
        'is_mandatory',
        'display_order',
        'witness_name',
        'witness_signature_path',
        'valid_from',
        'valid_till',
        'remarks',
    ];

    protected $casts = [
        'declaration_points' => 'array',
        'is_accepted' => 'boolean',
        'accepted_at' => 'datetime',
        'is_mandatory' => 'boolean',
        'display_order' => 'integer',
        'valid_from' => 'date',
        'valid_till' => 'date',
    ];

    /**
     * Get the loan application that owns the declaration.
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    /**
     * Get the applicant that owns the declaration.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Check if declaration is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->is_accepted;
    }

    /**
     * Check if declaration is mandatory.
     */
    public function isMandatory(): bool
    {
        return $this->is_mandatory;
    }

    /**
     * Check if declaration is valid.
     */
    public function isValid(): bool
    {
        $now = now()->toDateString();
        
        $afterValidFrom = is_null($this->valid_from) || $now >= $this->valid_from;
        $beforeValidTill = is_null($this->valid_till) || $now <= $this->valid_till;
        
        return $afterValidFrom && $beforeValidTill;
    }

    /**
     * Scope for accepted declarations.
     */
    public function scopeAccepted($query)
    {
        return $query->where('is_accepted', true);
    }

    /**
     * Scope for mandatory declarations.
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope ordered by display order.
     */
    public function scopeByDisplayOrder($query)
    {
        return $query->orderBy('display_order');
    }
}
