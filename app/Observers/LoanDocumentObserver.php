<?php

namespace App\Observers;

use App\Models\LoanDocument;
use App\Models\LoanStatusHistory;

class LoanDocumentObserver
{
    public function created(LoanDocument $doc): void
    {
        LoanStatusHistory::create([
            'loan_application_id' => $doc->loan_application_id,
            'previous_status' => null,
            'current_status' => $doc->loanApplication?->status,
            'action_type' => 'DOCUMENT_UPLOAD',
            'action_title' => 'Document Uploaded',
            'action_description' => 'Uploaded '.$doc->document_type,
            'performed_by' => auth()->id(),
            'actor_role' => $this->mapActorRole(),
            'stage' => 'DOCUMENTS',
            'attached_documents' => [$doc->id],
            'ip_address' => request()->ip() ?? null,
            'user_agent' => request()->userAgent() ?? null,
        ]);
    }

    public function updated(LoanDocument $doc): void
    {
        if ($doc->wasChanged('verified_at') || $doc->wasChanged('verified_by')) {
            LoanStatusHistory::create([
                'loan_application_id' => $doc->loan_application_id,
                'previous_status' => null,
                'current_status' => $doc->loanApplication?->status,
                'action_type' => 'VERIFICATION',
                'action_title' => 'Document Verified',
                'action_description' => 'Verified '.$doc->document_type,
                'performed_by' => auth()->id() ?? $doc->verified_by,
                'actor_role' => $this->mapActorRole(),
                'stage' => 'DOCUMENTS',
                'attached_documents' => [$doc->id],
                'ip_address' => request()->ip() ?? null,
                'user_agent' => request()->userAgent() ?? null,
            ]);
        }
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
