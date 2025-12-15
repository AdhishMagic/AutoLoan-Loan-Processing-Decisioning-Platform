<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanDocument extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'loan_application_id',
        'document_type',
        'file_path',
        'verified_by',
        'verified_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<LoanApplication, LoanDocument>
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * @return BelongsTo<User, LoanDocument>
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
