<?php

namespace App\Services\DocumentVerification;

use App\Models\LoanDocument;

final class DocumentTypeDetector
{
    /**
     * Returns one of the internal document types or 'unknown'.
     */
    public static function detect(string $ocrText): string
    {
        $t = OcrText::upper(OcrText::normalize($ocrText));

        $hasPan = (bool) preg_match('/\b[A-Z]{5}[0-9]{4}[A-Z]\b/', $t);
        if ($hasPan && (str_contains($t, 'INCOME TAX DEPARTMENT') || str_contains($t, 'GOVT. OF INDIA') || str_contains($t, 'GOVERNMENT OF INDIA'))) {
            return LoanDocument::TYPE_PAN;
        }

        $hasAadhaarDigits = (bool) preg_match('/\b\d{4}\s?\d{4}\s?\d{4}\b/', $t);
        $hasAadhaarMasked = (bool) preg_match('/\bX{4}\s?X{4}\s?\d{4}\b/', $t);
        if (($hasAadhaarDigits || $hasAadhaarMasked) && (str_contains($t, 'UIDAI') || str_contains($t, 'UNIQUE IDENTIFICATION') || str_contains($t, 'AADHAAR') || str_contains($t, 'GOVERNMENT OF INDIA'))) {
            return LoanDocument::TYPE_AADHAAR;
        }

        $looksLikeBank = (str_contains($t, 'STATEMENT') && (str_contains($t, 'ACCOUNT') || str_contains($t, 'A/C') || str_contains($t, 'IFSC')));
        if ($looksLikeBank) {
            return 'bank_statement';
        }

        $looksLikeSalary = (str_contains($t, 'PAY SLIP') || str_contains($t, 'PAYSLIP') || (str_contains($t, 'EARNINGS') && str_contains($t, 'DEDUCTIONS')));
        if ($looksLikeSalary) {
            return 'salary_slip';
        }

        return 'unknown';
    }
}
