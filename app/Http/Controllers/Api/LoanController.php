<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Resources\LoanResource;
use App\Models\LoanApplication;
use App\Models\LoanDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    /**
     * Submit a new loan application.
     */
    public function store(StoreLoanRequest $request)
    {
        $user = $request->user();

        $validated = $request->validated();

        $applicationNumber = 'LAP-' . Str::upper(Str::random(12));

        $payload = [
            'user_id' => $user->id,
            'application_number' => $applicationNumber,
            'application_date' => now()->toDateString(),
            'loan_product_type' => $validated['loan_product_type'],
            'requested_amount' => $validated['requested_amount'],
            'requested_tenure_months' => $validated['requested_tenure_months'],
            'monthly_income' => $validated['income'],
            'status' => 'UNDER_REVIEW',
            'submitted_at' => now(),
        ];

        // Optionally persist auxiliary fields safely
        if (isset($validated['employment_type'])) {
            $payload['customer_notes'] = trim(($validated['employment_type'] ?? '') !== ''
                ? '[employment_type] ' . $validated['employment_type']
                : '');
        }

        $loan = DB::transaction(function () use ($payload) {
            return LoanApplication::create($payload);
        });

        return (new LoanResource($loan))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Fetch loan details for the authenticated user.
     */
    public function show(Request $request, LoanApplication $loan)
    {
        Gate::authorize('view', $loan);
        return new LoanResource($loan);
    }

    /**
     * Securely download a loan document owned by the authenticated user.
     */
    public function downloadDocument(Request $request, LoanApplication $loan, string $filename)
    {
        Gate::authorize('view', $loan);

        $document = LoanDocument::query()
            ->where('loan_application_id', $loan->id)
            ->where('original_name', $filename)
            ->firstOrFail();

        Gate::authorize('view', $document);

        $path = $document->file_path;
        if (! is_string($path) || trim($path) === '' || ! Storage::exists($path)) {
            abort(404, 'Document not found');
        }

        return Storage::download($path, $document->original_name);
    }
}
