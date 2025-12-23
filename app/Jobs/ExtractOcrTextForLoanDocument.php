<?php

namespace App\Jobs;

use App\Models\LoanDocument;
use App\Services\Ocr\OcrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExtractOcrTextForLoanDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $documentId;

    public function __construct(string $documentId)
    {
        $this->documentId = $documentId;
    }

    public function handle(OcrService $ocr): void
    {
        /** @var LoanDocument|null $doc */
        $doc = LoanDocument::query()->where('id', $this->documentId)->first();
        if (! $doc) {
            Log::warning('ExtractOcrTextForLoanDocument: document not found', ['document_id' => $this->documentId]);
            return;
        }

        // If OCR is already present, do nothing.
        if (is_string($doc->ocr_text) && trim($doc->ocr_text) !== '') {
            return;
        }

        if (! is_string($doc->file_path) || trim($doc->file_path) === '') {
            return;
        }

        if (! Storage::disk('local')->exists($doc->file_path)) {
            Log::info('ExtractOcrTextForLoanDocument: file missing', ['path' => $doc->file_path]);
            return;
        }

        $absolutePath = Storage::disk('local')->path($doc->file_path);
        $text = $ocr->extractText($absolutePath);

        if (! is_string($text) || trim($text) === '') {
            return;
        }

        // Persist OCR text. This will trigger the observer to enqueue analysis.
        $doc->ocr_text = $text;
        $doc->save();
    }
}
