<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Resources\LoanResource;
use App\Services\LoanCacheService;
use App\Services\LoanProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class LoanController extends Controller
{
    public function __construct(
        private readonly LoanCacheService $cache,
        private readonly LoanProcessingService $processing,
    ) {}

    /**
     * Submit a new loan application.
     */
    public function store(StoreLoanRequest $request)
    {
        $dto = \App\DTOs\LoanDto::fromRequest($request);
        $result = $this->processing->submit($dto);
        $loan = $this->processing->getLoan($result['loan_id']);

        return response()->json([
            'loan' => new LoanResource($loan),
            'decision' => $result,
        ], 201);
    }

    /**
     * Fetch loan details for the authenticated user.
     */
    public function show(Request $request, string $loan)
    {
        $loanModel = $this->processing->getLoan($loan);
        Gate::authorize('view', $loanModel);

        return new LoanResource($loanModel);
    }

    /**
     * Fetch loan status (cached).
     */
    public function status(Request $request, string $loan)
    {
        $loanModel = $this->processing->getLoan($loan);
        Gate::authorize('view', $loanModel);

        $status = $this->cache->getLoanStatus((string) $loanModel->id, function () use ($loanModel) {
            return (string) $loanModel->status;
        });

        return response()->json([
            'loan_id' => (string) $loanModel->id,
            'status' => $status,
        ]);
    }

    /**
     * Fetch KYC lookup result (cached).
     */
    public function kyc(Request $request, string $loan)
    {
        $loanModel = $this->processing->getLoan($loan);
        Gate::authorize('view', $loanModel);

        $result = $this->cache->getKycResult((string) $loanModel->id, function () use ($loanModel) {
            return $this->processing->getKycSummary((string) $loanModel->id);
        });

        return response()->json($result);
    }

    /**
     * Securely download a loan document owned by the authenticated user.
     */
    public function downloadDocument(Request $request, string $loan, string $filename)
    {
        $loanModel = $this->processing->getLoan($loan);
        Gate::authorize('view', $loanModel);

        $document = $this->processing->findDocumentForDownload((string) $loanModel->id, $filename);
        Gate::authorize('view', $document);

        $path = $document->file_path;
        if (! is_string($path) || trim($path) === '' || ! Storage::exists($path)) {
            abort(404, 'Document not found');
        }

        return Storage::download($path, $document->original_name);
    }
}
