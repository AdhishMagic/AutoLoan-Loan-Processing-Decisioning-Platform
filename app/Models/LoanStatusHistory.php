<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanStatusHistory extends Model
{
    use HasUuids;

    /**
     * Explicit table to match migration (singular name).
     */
    protected $table = 'loan_status_history';

    protected $fillable = [
        'loan_application_id',
        'previous_status',
        'current_status',
        'action_type',
        'action_title',
        'action_description',
        'reason',
        'performed_by',
        'actor_role',
        'stage',
        'stage_order',
        'assigned_from',
        'assigned_to',
        'action_timestamp',
        'time_taken_minutes',
        'ip_address',
        'user_agent',
        'additional_data',
        'notification_sent',
        'notification_sent_at',
        'attached_documents',
        'priority',
        'sla_deadline',
        'is_sla_breached',
    ];

    protected $casts = [
        'action_timestamp' => 'datetime',
        'time_taken_minutes' => 'integer',
        'additional_data' => 'array',
        'notification_sent' => 'boolean',
        'notification_sent_at' => 'datetime',
        'attached_documents' => 'array',
        'sla_deadline' => 'datetime',
        'is_sla_breached' => 'boolean',
        'stage_order' => 'integer',
    ];

    /**
     * Get the loan application that owns the history record.
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    /**
     * Get the user who performed the action.
     */
    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Get the user from whom it was assigned.
     */
    public function assignedFrom(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_from');
    }

    /**
     * Get the user to whom it was assigned.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope for specific loan application.
     */
    public function scopeForLoan($query, $loanApplicationId)
    {
        return $query->where('loan_application_id', $loanApplicationId);
    }

    /**
     * Scope ordered by timeline.
     */
    public function scopeTimeline($query)
    {
        return $query->orderBy('action_timestamp', 'desc');
    }

    /**
     * Scope for SLA breached records.
     */
    public function scopeSlaBreached($query)
    {
        return $query->where('is_sla_breached', true);
    }

    /**
     * Scope for specific action types.
     */
    public function scopeActionType($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }
}
