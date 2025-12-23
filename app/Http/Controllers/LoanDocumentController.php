<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanDocumentRequest;
use App\Models\LoanApplication;
use App\Models\LoanDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class LoanDocumentController extends Controller
{
    public function store(StoreLoanDocumentRequest $request, LoanApplication $loan): RedirectResponse
    {
        $this->authorize('create', [LoanDocument::class, $loan]);

        $documentType = (string) $request->validated('document_type');
        $file = $request->file('file');

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        $safeExtension = in_array($extension, ['pdf', 'jpg', 'jpeg', 'png'], true) ? $extension : 'bin';

        $dir = "loans/{$loan->id}";
        $safeFilename = $documentType.'.'.$safeExtension;

        $existing = LoanDocument::query()
            ->where('loan_application_id', $loan->id)
            ->where('document_type', $documentType)
            ->first();

        if ($existing && $existing->file_path && Storage::disk('local')->exists($existing->file_path)) {
            Storage::disk('local')->delete($existing->file_path);
        }

        $storedPath = Storage::disk('local')->putFileAs($dir, $file, $safeFilename);

        /** @var LoanDocument $document */
        $document = LoanDocument::query()->updateOrCreate(
            [
                'loan_application_id' => $loan->id,
                'document_type' => $documentType,
            ],
            [
                'user_id' => $request->user()->id,
                'file_path' => $storedPath,
                'original_name' => $file->getClientOriginalName(),
                'verified_by' => null,
                'verified_at' => null,
            ]
        );

        return redirect()
            ->back()
            ->with('status', "Document '{$document->document_type}' uploaded successfully.");
    }

    /**
     * Generates a 30-minute signed URL. For HTML requests, redirects to it.
     */
    public function signedDownloadLink(Request $request, LoanDocument $document): Response|RedirectResponse
    {
        $this->authorize('view', $document);

        $expiresAt = now()->addMinutes(30);

        $url = URL::temporarySignedRoute(
            'loan.document.download',
            $expiresAt,
            ['document' => $document->id]
        );

        if ($request->expectsJson() || $request->boolean('json')) {
            return response()->json([
                'url' => $url,
                'expires_at' => $expiresAt->toISOString(),
            ]);
        }

        return redirect()->to($url);
    }

    /**
     * Serves the file via a signed, tamper-proof URL.
     */
    public function download(Request $request, LoanDocument $document): BinaryFileResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired signed URL.');
        }

        $this->authorize('view', $document);

        $expectedPrefix = 'loans/'.$document->loan_application_id.'/';
        if (! Str::startsWith((string) $document->file_path, $expectedPrefix)) {
            abort(403, 'Invalid document path.');
        }

        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Document file not found.');
        }

        $absolutePath = Storage::disk('local')->path($document->file_path);
        $downloadName = $document->original_name ?: basename($document->file_path);

        return response()->download($absolutePath, $downloadName);
    }
}
