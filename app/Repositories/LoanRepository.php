<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\LoanApplication;
use App\Models\LoanDocument;
use App\Repositories\Contracts\LoanRepositoryInterface;

final class LoanRepository implements LoanRepositoryInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): LoanApplication
    {
        return LoanApplication::query()->create($data);
    }

    public function findById(string $loanId): ?LoanApplication
    {
        return LoanApplication::query()->find($loanId);
    }

    public function updateStatus(LoanApplication $loan, string $status, array $attributes = []): LoanApplication
    {
        $loan->fill($attributes);
        $loan->status = $status;
        $loan->save();

        return $loan;
    }

    public function createDocument(
        LoanApplication $loan,
        int $userId,
        string $documentType,
        string $filePath,
        string $originalName,
    ): LoanDocument {
        return LoanDocument::query()->create([
            'loan_application_id' => $loan->id,
            'user_id' => $userId,
            'document_type' => $documentType,
            'file_path' => $filePath,
            'original_name' => $originalName,
        ]);
    }

    public function findDocumentByOriginalName(LoanApplication $loan, string $originalName): ?LoanDocument
    {
        return LoanDocument::query()
            ->where('loan_application_id', $loan->id)
            ->where('original_name', $originalName)
            ->first();
    }
}
