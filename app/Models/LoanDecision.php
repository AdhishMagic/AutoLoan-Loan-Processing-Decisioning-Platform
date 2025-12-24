<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanDecision extends Model
{
    use HasUuids;

    /**
     * The primary key is a UUID (see migration).
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /** @var array<int, string> */
    protected $fillable = [
        'loan_application_id',
        'decision',
        'remarks',
        'decided_by',
        // Automated underwriting fields (nullable for manual decisions)
        'source',
        'engine_name',
        'engine_version',
        'underwriting_rule_id',
        'underwriting_rule_name',
        'underwriting_rule_snapshot',
        'facts_snapshot',
        'score',
        'decision_status',
        'reasons',
        'trace',
        'executed_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'underwriting_rule_snapshot' => 'array',
        'facts_snapshot' => 'array',
        'reasons' => 'array',
        'trace' => 'array',
        'executed_at' => 'datetime',
        'score' => 'integer',
    ];

    /**
     * @return BelongsTo<LoanApplication, LoanDecision>
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * @return BelongsTo<User, LoanDecision>
     */
    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
