<?php

namespace App\Observers;

use App\Jobs\AnalyzeLoanDocuments;
use App\Jobs\ExtractOcrTextForLoanDocument;
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
            'performed_by' => auth()->id() ?? $doc->user_id,
            'actor_role' => $this->mapActorRole(),
            'stage' => 'DOCUMENTS',
            'attached_documents' => [$doc->id],
            'ip_address' => request()->ip() ?? null,
            'user_agent' => request()->userAgent() ?? null,
        ]);

        // Best-effort OCR ingestion (async). If OCR is produced, it will trigger analysis.
        if (! is_string($doc->ocr_text) || trim($doc->ocr_text) === '') {
            if (is_string($doc->file_path) && trim($doc->file_path) !== '') {
                ExtractOcrTextForLoanDocument::dispatch($doc->id);
            }
        }

        // If OCR text already exists at create-time, auto-analyze.
        if (is_string($doc->ocr_text) && trim($doc->ocr_text) !== '') {
            AnalyzeLoanDocuments::dispatch($doc->loan_application_id);
        }
    }

    public function updated(LoanDocument $doc): void
    {
        // If the underlying file changes, (re)run OCR extraction asynchronously.
        if ($doc->wasChanged('file_path')) {
            if (! is_string($doc->ocr_text) || trim($doc->ocr_text) === '') {
                if (is_string($doc->file_path) && trim($doc->file_path) !== '') {
                    ExtractOcrTextForLoanDocument::dispatch($doc->id);
                }
            }
        }

        // Auto-analyze when OCR text is set/changed.
        // Guard: analysis updates other columns, but should not touch ocr_text.
        if ($doc->wasChanged('ocr_text')) {
            if (is_string($doc->ocr_text) && trim($doc->ocr_text) !== '') {
                AnalyzeLoanDocuments::dispatch($doc->loan_application_id);
            }
        }

        if ($doc->wasChanged('verified_at') || $doc->wasChanged('verified_by')) {
            LoanStatusHistory::create([
                'loan_application_id' => $doc->loan_application_id,
                'previous_status' => null,
                'current_status' => $doc->loanApplication?->status,
                'action_type' => 'VERIFICATION',
                'action_title' => 'Document Verified',
                'action_description' => 'Verified '.$doc->document_type,
                'performed_by' => auth()->id() ?? $doc->verified_by ?? $doc->user_id,
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
