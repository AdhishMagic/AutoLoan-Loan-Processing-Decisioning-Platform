<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\LoanApplication;
use App\Models\LoanStatusHistory;

class LoanApplicationObserver
{
    public function created(LoanApplication $loan): void
    {
        // Timeline entry
        LoanStatusHistory::create([
            'loan_application_id' => $loan->id,
            'previous_status' => null,
            'current_status' => $loan->status ?? 'draft',
            'action_type' => 'STATUS_CHANGE',
            'action_title' => 'Application Created',
            'action_description' => 'Loan application has been created',
            'performed_by' => auth()->id() ?? $loan->user_id,
            'actor_role' => $this->mapActorRole(),
            'ip_address' => request()->ip() ?? null,
            'user_agent' => request()->userAgent() ?? null,
        ]);

        // Audit log
        AuditLog::create([
            'user_id' => auth()->id() ?? $loan->user_id,
            'action' => 'loan.created',
            'ip_address' => request()->ip() ?? null,
            'user_agent' => request()->userAgent() ?? null,
            'metadata' => [
                'loan_id' => $loan->id,
                'status' => $loan->status,
            ],
        ]);
    }

    public function updated(LoanApplication $loan): void
    {
        if ($loan->wasChanged('status')) {
            LoanStatusHistory::create([
                'loan_application_id' => $loan->id,
                'previous_status' => $loan->getOriginal('status'),
                'current_status' => $loan->status,
                'action_type' => 'STATUS_CHANGE',
                'action_title' => 'Status Updated',
                'action_description' => 'Status changed from '.($loan->getOriginal('status') ?? 'N/A').' to '.$loan->status,
                'performed_by' => auth()->id() ?? $loan->user_id,
                'actor_role' => $this->mapActorRole(),
                'ip_address' => request()->ip() ?? null,
                'user_agent' => request()->userAgent() ?? null,
            ]);
        }

        if ($loan->wasChanged('submitted_at') && ! is_null($loan->submitted_at)) {
            LoanStatusHistory::create([
                'loan_application_id' => $loan->id,
                'previous_status' => $loan->getOriginal('status'),
                'current_status' => $loan->status,
                'action_type' => 'SUBMISSION',
                'action_title' => 'Application Submitted',
                'action_description' => 'Application submitted for review',
                'performed_by' => auth()->id() ?? $loan->user_id,
                'actor_role' => $this->mapActorRole(),
                'stage' => 'APPLICATION',
                'ip_address' => request()->ip() ?? null,
                'user_agent' => request()->userAgent() ?? null,
            ]);
        }

        AuditLog::create([
            'user_id' => auth()->id() ?? $loan->user_id,
            'action' => 'loan.updated',
            'ip_address' => request()->ip() ?? null,
            'user_agent' => request()->userAgent() ?? null,
            'metadata' => [
                'loan_id' => $loan->id,
                'changed' => $loan->getChanges(),
            ],
        ]);
    }

    public function deleted(LoanApplication $loan): void
    {
        // For soft delete, record in audit log
        AuditLog::create([
            'user_id' => auth()->id() ?? $loan->user_id,
            'action' => 'loan.deleted',
            'ip_address' => request()->ip() ?? null,
            'user_agent' => request()->userAgent() ?? null,
            'metadata' => [
                'loan_id' => $loan->id,
            ],
        ]);
    }

    private function mapActorRole(): string
    {
        $role = auth()->user()?->role?->name;
        return match ($role) {
            'admin' => 'ADMIN',
            'manager' => 'LOAN_OFFICER',
            'customer_service' => 'SYSTEM',
            'user' => 'APPLICANT',
            default => 'SYSTEM',
        };
    }
}
