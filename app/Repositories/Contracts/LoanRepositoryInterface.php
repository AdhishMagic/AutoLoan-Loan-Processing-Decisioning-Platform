<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\LoanApplication;
use App\Models\LoanDocument;

interface LoanRepositoryInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): LoanApplication;

    public function findById(string $loanId): ?LoanApplication;

    public function updateStatus(LoanApplication $loan, string $status, array $attributes = []): LoanApplication;

    public function createDocument(
        LoanApplication $loan,
        int $userId,
        string $documentType,
        string $filePath,
        string $originalName,
    ): LoanDocument;

    public function findDocumentByOriginalName(LoanApplication $loan, string $originalName): ?LoanDocument;
}
