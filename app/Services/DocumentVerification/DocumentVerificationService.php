<?php

namespace App\Services\DocumentVerification;

use App\Models\Applicant;
use App\Models\LoanApplication;
use App\Models\LoanDocument;

class DocumentVerificationService
{
    public const ANALYSIS_VERSION = '1.0';

    /**
     * Analyze all documents on a loan that have OCR text.
     *
     * @return array{report: string, overall: array<string, mixed>, documents: array<int, array<string, mixed>>}
     */
    public function analyzeLoan(LoanApplication $loan): array
    {
        $loan->loadMissing(['documents', 'applicants']);

        /** @var Applicant|null $applicant */
        $applicant = $loan->applicants->firstWhere('applicant_role', 'PRIMARY');

        $docs = $loan->documents;

        // Precompute hashes for uniqueness checks.
        $hashCounts = [];
        foreach ($docs as $doc) {
            if (! is_string($doc->ocr_text) || trim($doc->ocr_text) === '') {
                continue;
            }
            $normalized = OcrText::normalize($doc->ocr_text);
            $hash = OcrText::sha256($normalized);
            $hashCounts[$hash] = ($hashCounts[$hash] ?? 0) + 1;
        }

        $docResults = [];
        $trustScores = [];

        foreach ($docs as $doc) {
            if (! is_string($doc->ocr_text) || trim($doc->ocr_text) === '') {
                $docResults[] = $this->emptyResultForMissingOcr($doc);
                continue;
            }

            $docResults[] = $this->analyzeDocument($doc, $applicant, $loan, $hashCounts);
            if (isset($docResults[array_key_last($docResults)]['trust_score'])) {
                $trustScores[] = (int) $docResults[array_key_last($docResults)]['trust_score'];
            }
        }

        $overallTrust = empty($trustScores) ? 0 : (int) round(array_sum($trustScores) / count($trustScores));

        $report = $this->formatLoanOfficerReport($loan, $applicant, $docResults, $overallTrust);

        return [
            'report' => $report,
            'overall' => [
                'overall_trust_score' => $overallTrust,
                'risk_level' => Scoring::riskLevel($overallTrust),
                'recommendation' => Scoring::recommendation($overallTrust),
            ],
            'documents' => $docResults,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function analyzeDocument(LoanDocument $doc, ?Applicant $applicant, LoanApplication $loan, array $hashCounts): array
    {
        $raw = (string) $doc->ocr_text;
        $normalized = OcrText::normalize($raw);
        $hash = OcrText::sha256($normalized);

        $docType = $doc->document_type ?: DocumentTypeDetector::detect($raw);
        $detected = DocumentTypeDetector::detect($raw);

        $tooShort = mb_strlen($normalized, 'UTF-8') < 80;
        $isDuplicate = ($hashCounts[$hash] ?? 0) > 1;

        $extracted = [];
        $verification = [];
        $remarks = [];
        $matchesUser = null;

        $upper = OcrText::upper($normalized);

        if ($docType === LoanDocument::TYPE_PAN || $detected === LoanDocument::TYPE_PAN) {
            $pan = Parsers::extractPanNumber($upper);
            $name = Parsers::extractLabeledValue($normalized, 'NAME')
                ?? Parsers::extractLabeledValue($normalized, 'NAME OF CARDHOLDER');
            $dob = Parsers::extractLabeledValue($normalized, 'DATE OF BIRTH')
                ?? Parsers::extractLabeledValue($normalized, 'DOB')
                ?? Parsers::extractDob($normalized);

            $extracted = [
                'pan' => $pan,
                'name' => $name,
                'dob' => $dob,
            ];

            if ($applicant) {
                $matches = true;
                if ($pan !== null && $applicant->pan_number) {
                    $matches = $matches && (OcrText::upper($pan) === OcrText::upper((string) $applicant->pan_number));
                }
                if ($dob !== null && $applicant->date_of_birth) {
                    $matches = $matches && ($this->normalizeDate($dob) === $applicant->date_of_birth->format('Y-m-d'));
                }
                if ($name !== null) {
                    $matches = $matches && $this->nameLikelyMatches($name, $applicant->full_name);
                }
                $matchesUser = $matches;
            }

            $verification = [
                'pan_format_valid' => $pan ? (bool) preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $pan) : false,
                'matches_user_data' => $matchesUser,
            ];

            if ($pan === null) {
                $remarks[] = 'PAN number not found in OCR.';
            }
            if ($name === null) {
                $remarks[] = 'Name not found in OCR.';
            }
            if ($dob === null) {
                $remarks[] = 'DOB not found in OCR.';
            }
        } elseif ($docType === LoanDocument::TYPE_AADHAAR || $detected === LoanDocument::TYPE_AADHAAR) {
            $aadhaar = Parsers::extractAadhaar($upper);
            $name = Parsers::extractLabeledValue($normalized, 'NAME')
                ?? Parsers::extractLabeledValue($normalized, 'NAME:');
            $dob = Parsers::extractLabeledValue($normalized, 'DOB')
                ?? Parsers::extractLabeledValue($normalized, 'DATE OF BIRTH')
                ?? Parsers::extractDob($normalized);
            $yob = Parsers::extractYearOfBirth($normalized);

            $hasGovKeywords = str_contains($upper, 'UIDAI')
                || str_contains($upper, 'GOVERNMENT OF INDIA')
                || str_contains($upper, 'UNIQUE IDENTIFICATION')
                || str_contains($upper, 'AADHAAR');

            $maskValid = Parsers::isAadhaarMaskingValid($aadhaar);

            $extracted = [
                'aadhaar' => $aadhaar,
                'name' => $name,
                'dob' => $dob,
                'yob' => $yob,
            ];

            if ($applicant) {
                $matches = true;
                if ($aadhaar !== null && $applicant->aadhaar_number) {
                    $appA = preg_replace('/\D+/', '', (string) $applicant->aadhaar_number) ?? '';
                    $docA = preg_replace('/\D+/', '', (string) $aadhaar) ?? '';
                    if (strlen($docA) === 12) {
                        $matches = $matches && ($docA === $appA);
                    } elseif (strlen($docA) === 4) {
                        $matches = $matches && str_ends_with($appA, $docA);
                    }
                }
                if ($dob !== null && $applicant->date_of_birth) {
                    $matches = $matches && ($this->normalizeDate($dob) === $applicant->date_of_birth->format('Y-m-d'));
                } elseif ($yob !== null && $applicant->date_of_birth) {
                    $matches = $matches && ((string) $applicant->date_of_birth->format('Y') === (string) $yob);
                }
                if ($name !== null) {
                    $matches = $matches && $this->nameLikelyMatches($name, $applicant->full_name);
                }
                $matchesUser = $matches;
            }

            $verification = [
                'masking_valid' => $maskValid,
                'has_government_keywords' => $hasGovKeywords,
                'matches_user_data' => $matchesUser,
            ];

            if ($aadhaar === null) {
                $remarks[] = 'Aadhaar number not found in OCR.';
            } elseif (! $maskValid) {
                $remarks[] = 'Aadhaar number present but masking/format looks invalid.';
            }
            if (! $hasGovKeywords) {
                $remarks[] = 'Government/UIDAI keywords not confidently detected.';
            }
        } else {
            // Income proof can be a salary slip or bank statement. Detect and extract conservatively.
            $det = $detected;
            if ($det === 'salary_slip') {
                $employer = Parsers::extractLabeledValue($normalized, 'EMPLOYER')
                    ?? Parsers::extractLabeledValue($normalized, 'COMPANY')
                    ?? $this->guessHeaderName($normalized);

                $month = Parsers::extractMonthYear($normalized);

                $net = Parsers::extractMoney($normalized, ['NET PAY', 'NET SALARY', 'TAKE HOME', 'NET AMOUNT']);
                $gross = Parsers::extractMoney($normalized, ['GROSS', 'GROSS PAY', 'GROSS SALARY', 'GROSS EARNINGS']);

                $declared = $loan->monthly_income ? (float) $loan->monthly_income : null;
                $incomeMatch = null;
                if ($declared !== null && $net !== null) {
                    // Conservative: allow +/- 25% tolerance (OCR often drops decimals/commas)
                    $incomeMatch = ($net >= (0.75 * $declared) && $net <= (1.25 * $declared));
                }

                $extracted = [
                    'detected_subtype' => 'salary_slip',
                    'employer' => $employer,
                    'month' => $month,
                    'net_salary' => $net,
                    'gross_salary' => $gross,
                ];

                $verification = [
                    'income_match_declared' => $incomeMatch,
                ];

                if ($employer === null) {
                    $remarks[] = 'Employer name not confidently extracted.';
                }
                if ($month === null) {
                    $remarks[] = 'Pay month/year not found.';
                }
                if ($net === null) {
                    $remarks[] = 'Net salary not found.';
                }
            } elseif ($det === 'bank_statement') {
                $bankName = $this->extractBankName($normalized);
                $holder = Parsers::extractLabeledValue($normalized, 'ACCOUNT NAME')
                    ?? Parsers::extractLabeledValue($normalized, 'NAME');

                $avgBalance = Parsers::extractMoney($normalized, ['AVERAGE BALANCE', 'AVG BALANCE', 'MONTHLY AVERAGE BALANCE']);

                $salaryCredits = (bool) preg_match('/\bSALARY\b/', OcrText::upper($normalized));

                $extracted = [
                    'detected_subtype' => 'bank_statement',
                    'bank_name' => $bankName,
                    'account_holder_name' => $holder,
                    'avg_balance' => $avgBalance,
                    'salary_credits_found' => $salaryCredits,
                ];

                if ($applicant && $holder !== null) {
                    $matchesUser = $this->nameLikelyMatches($holder, $applicant->full_name);
                }

                $verification = [
                    'name_matches_applicant' => $matchesUser,
                    'salary_credits_found' => $salaryCredits,
                ];

                if ($bankName === null) {
                    $remarks[] = 'Bank name not confidently detected.';
                }
                if ($holder === null) {
                    $remarks[] = 'Account holder name not found.';
                }
            } else {
                $extracted = [
                    'detected_subtype' => $det,
                ];
                $remarks[] = 'Document type could not be confidently identified from OCR.';
            }
        }

        $hasKeyFields = $this->hasKeyFields($docType, $extracted, $detected);
        $hasGovKeywords = $this->hasGovKeywords($docType, $upper);
        $hasStructuredLabels = $this->hasStructuredLabels($upper);

        $auth = Scoring::authenticityScore([
            'hasKeyFields' => $hasKeyFields,
            'hasGovKeywords' => $hasGovKeywords,
            'hasStructuredLabels' => $hasStructuredLabels,
            'matchesUser' => $matchesUser,
            'ocrWeak' => $tooShort,
        ]);

        $uniq = Scoring::uniquenessScore([
            'hasHash' => true,
            'tooShort' => $tooShort,
            'isDuplicateOnLoan' => $isDuplicate,
        ]);

        $trust = Scoring::trustScore($auth, $uniq, ['matchesUser' => $matchesUser]);

        // Persist back to the document (best-effort; caller may choose to save).
        $doc->ocr_normalized_text = $normalized;
        $doc->ocr_hash = $hash;
        $doc->extracted_data = $extracted;
        $doc->verification_result = [
            'doc_type' => $docType,
            'detected_type' => $detected,
            'signals' => [
                'has_key_fields' => $hasKeyFields,
                'has_gov_keywords' => $hasGovKeywords,
                'has_structured_labels' => $hasStructuredLabels,
                'ocr_too_short' => $tooShort,
                'duplicate_on_loan' => $isDuplicate,
                'matches_user_data' => $matchesUser,
            ],
            'remarks' => $remarks,
            'validation' => $verification,
        ];
        $doc->authenticity_score = $auth;
        $doc->uniqueness_score = $uniq;
        $doc->trust_score = $trust;
        $doc->analyzed_at = now();
        $doc->analysis_version = self::ANALYSIS_VERSION;
        $doc->save();

        return [
            'document_id' => $doc->id,
            'document_type' => $docType,
            'detected_type' => $detected,
            'extracted_data' => $extracted,
            'verification_result' => $doc->verification_result,
            'authenticity_score' => $auth,
            'uniqueness_score' => $uniq,
            'trust_score' => $trust,
        ];
    }

    /**
     * Strict Loan Officer format.
     *
     * @param array<int, array<string, mixed>> $docResults
     */
    public function formatLoanOfficerReport(LoanApplication $loan, ?Applicant $applicant, array $docResults, int $overallTrustScore): string
    {
        $applicantName = $applicant?->full_name ?: 'Not Found';

        $pan = $this->pickDoc($docResults, LoanDocument::TYPE_PAN);
        $aadhaar = $this->pickDoc($docResults, LoanDocument::TYPE_AADHAAR);

        $salarySlip = $this->pickDocBySubtype($docResults, 'salary_slip');
        $bankStmt = $this->pickDocBySubtype($docResults, 'bank_statement');

        $declaredIncome = $loan->monthly_income !== null ? (string) $loan->monthly_income : 'Not Found';

        $lines = [];
        $lines[] = '📌 Applicant Overview';
        $lines[] = '';
        $lines[] = 'Applicant Name: '.$applicantName;
        $lines[] = 'Loan Type: '.((string) ($loan->loan_type ?? 'Not Found'));
        $lines[] = 'Loan Amount: '.((string) ($loan->requested_amount ?? 'Not Found'));
        $lines[] = 'Declared Income: '.$declaredIncome;
        $lines[] = 'Application Status: '.((string) ($loan->status ?? 'Not Found'));
        $lines[] = '';
        $lines[] = '📂 Document Verification Summary';

        // PAN
        $lines[] = 'PAN Card';
        $lines[] = '';
        $lines[] = 'Extracted PAN: '.$this->val($pan, ['extracted_data', 'pan']);
        $lines[] = 'Extracted Name: '.$this->val($pan, ['extracted_data', 'name']);
        $lines[] = 'DOB: '.$this->val($pan, ['extracted_data', 'dob']);
        $match = $this->boolIcon($this->valRaw($pan, ['verification_result', 'validation', 'matches_user_data']));
        $lines[] = 'Match with user data: '.$match;
        $lines[] = 'Authenticity Score: '.$this->pct($pan, 'authenticity_score');
        $lines[] = 'Remarks: '.$this->remarks($pan);
        $lines[] = '';

        // Aadhaar
        $lines[] = 'Aadhaar Card';
        $lines[] = '';
        $lines[] = 'Extracted Aadhaar: '.$this->val($aadhaar, ['extracted_data', 'aadhaar']);
        $lines[] = 'Extracted Name: '.$this->val($aadhaar, ['extracted_data', 'name']);
        $lines[] = 'DOB/YOB: '.$this->val($aadhaar, ['extracted_data', 'dob'], $this->val($aadhaar, ['extracted_data', 'yob']));
        $mask = $this->boolIcon($this->valRaw($aadhaar, ['verification_result', 'validation', 'masking_valid']));
        $lines[] = 'Masking Valid: '.$mask;
        $lines[] = 'Authenticity Score: '.$this->pct($aadhaar, 'authenticity_score');
        $lines[] = 'Remarks: '.$this->remarks($aadhaar);
        $lines[] = '';

        // Salary Slip
        $lines[] = 'Salary Slip';
        $lines[] = '';
        $lines[] = 'Employer: '.$this->val($salarySlip, ['extracted_data', 'employer']);
        $net = $this->money($this->valRaw($salarySlip, ['extracted_data', 'net_salary']));
        $gross = $this->money($this->valRaw($salarySlip, ['extracted_data', 'gross_salary']));
        $lines[] = 'Salary (Net/Gross): '.$net.' / '.$gross;
        $lines[] = 'Month: '.$this->val($salarySlip, ['extracted_data', 'month']);
        $incomeMatch = $this->boolIcon($this->valRaw($salarySlip, ['verification_result', 'validation', 'income_match_declared']));
        $lines[] = 'Income Match: '.$incomeMatch;
        $lines[] = 'Authenticity Score: '.$this->pct($salarySlip, 'authenticity_score');
        $lines[] = 'Remarks: '.$this->remarks($salarySlip);
        $lines[] = '';

        // Bank Statement
        $lines[] = 'Bank Statement';
        $lines[] = '';
        $lines[] = 'Bank Name: '.$this->val($bankStmt, ['extracted_data', 'bank_name']);
        $avg = $this->money($this->valRaw($bankStmt, ['extracted_data', 'avg_balance']));
        $lines[] = 'Avg Balance: '.$avg;
        $sal = $this->boolIcon($this->valRaw($bankStmt, ['extracted_data', 'salary_credits_found']));
        $lines[] = 'Salary Credits Found: '.$sal;
        $lines[] = 'Authenticity Score: '.$this->pct($bankStmt, 'authenticity_score');
        $lines[] = 'Remarks: '.$this->remarks($bankStmt);
        $lines[] = '';

        $lines[] = '📈 Final Risk Assessment';
        $lines[] = '';
        $lines[] = 'Overall Trust Score: '.Scoring::clamp($overallTrustScore).'%';
        $lines[] = 'Risk Level: '.Scoring::riskLevel($overallTrustScore);
        $lines[] = 'Recommendation:';
        $lines[] = Scoring::recommendation($overallTrustScore);

        return implode("\n", $lines);
    }

    private function emptyResultForMissingOcr(LoanDocument $doc): array
    {
        return [
            'document_id' => $doc->id,
            'document_type' => $doc->document_type,
            'detected_type' => 'unknown',
            'extracted_data' => [],
            'verification_result' => [
                'remarks' => ['OCR text not provided.'],
            ],
            'authenticity_score' => 0,
            'uniqueness_score' => 0,
            'trust_score' => 0,
        ];
    }

    /** @param array<int, array<string, mixed>> $docResults */
    private function pickDoc(array $docResults, string $documentType): ?array
    {
        foreach ($docResults as $r) {
            if (($r['document_type'] ?? null) === $documentType) {
                return $r;
            }
        }
        return null;
    }

    /** @param array<int, array<string, mixed>> $docResults */
    private function pickDocBySubtype(array $docResults, string $subtype): ?array
    {
        foreach ($docResults as $r) {
            $detected = $r['detected_type'] ?? null;
            $exSubtype = $r['extracted_data']['detected_subtype'] ?? null;
            if ($detected === $subtype || $exSubtype === $subtype) {
                return $r;
            }
        }
        return null;
    }

    private function val(?array $doc, array $path, ?string $fallback = null): string
    {
        $v = $this->valRaw($doc, $path);
        if ($v === null || $v === '' || (is_array($v) && empty($v))) {
            return $fallback ?? 'Not Found';
        }
        if (is_bool($v)) {
            return $v ? 'Yes' : 'No';
        }
        if (is_scalar($v)) {
            return (string) $v;
        }
        return $fallback ?? 'Uncertain';
    }

    private function valRaw(?array $doc, array $path): mixed
    {
        if ($doc === null) {
            return null;
        }

        $cur = $doc;
        foreach ($path as $k) {
            if (! is_array($cur) || ! array_key_exists($k, $cur)) {
                return null;
            }
            $cur = $cur[$k];
        }

        return $cur;
    }

    private function pct(?array $doc, string $key): string
    {
        $v = $doc[$key] ?? null;
        if (! is_int($v) && ! is_numeric($v)) {
            return '0%';
        }
        return Scoring::clamp((int) $v).'%';
    }

    private function boolIcon(mixed $value): string
    {
        if ($value === true) {
            return '✅';
        }
        if ($value === false) {
            return '❌';
        }
        return 'Uncertain';
    }

    private function remarks(?array $doc): string
    {
        $r = $this->valRaw($doc, ['verification_result', 'remarks']);
        if (is_array($r) && ! empty($r)) {
            return implode(' ', array_map('strval', $r));
        }
        return 'No issues detected.';
    }

    private function normalizeDate(string $date): ?string
    {
        $date = trim($date);
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $m)) {
            return $m[3].'-'.$m[2].'-'.$m[1];
        }
        return null;
    }

    private function nameLikelyMatches(string $docName, string $applicantName): bool
    {
        $a = preg_replace('/[^A-Z ]+/', '', OcrText::upper($applicantName)) ?? '';
        $d = preg_replace('/[^A-Z ]+/', '', OcrText::upper($docName)) ?? '';

        $a = trim(preg_replace('/\s+/', ' ', $a) ?? $a);
        $d = trim(preg_replace('/\s+/', ' ', $d) ?? $d);

        if ($a === '' || $d === '') {
            return false;
        }

        // Conservative: require all applicant tokens to appear in doc name (order-independent)
        $tokens = array_values(array_filter(explode(' ', $a), fn ($t) => strlen($t) >= 2));
        foreach ($tokens as $t) {
            if (! str_contains($d, $t)) {
                return false;
            }
        }
        return true;
    }

    private function hasGovKeywords(string $docType, string $upperText): bool
    {
        if ($docType === LoanDocument::TYPE_PAN) {
            return str_contains($upperText, 'INCOME TAX') || str_contains($upperText, 'GOVERNMENT OF INDIA');
        }
        if ($docType === LoanDocument::TYPE_AADHAAR) {
            return str_contains($upperText, 'UIDAI') || str_contains($upperText, 'UNIQUE IDENTIFICATION') || str_contains($upperText, 'GOVERNMENT OF INDIA');
        }
        return false;
    }

    private function hasStructuredLabels(string $upperText): bool
    {
        $labels = ['NAME', 'DOB', 'DATE OF BIRTH', 'ACCOUNT', 'STATEMENT', 'NET', 'GROSS', 'EARNINGS', 'DEDUCTIONS'];
        $hits = 0;
        foreach ($labels as $l) {
            if (str_contains($upperText, $l)) {
                $hits++;
            }
        }
        return $hits >= 2;
    }

    private function hasKeyFields(string $docType, array $extracted, string $detected): bool
    {
        if ($docType === LoanDocument::TYPE_PAN || $detected === LoanDocument::TYPE_PAN) {
            return ! empty($extracted['pan']) && ! empty($extracted['name']);
        }
        if ($docType === LoanDocument::TYPE_AADHAAR || $detected === LoanDocument::TYPE_AADHAAR) {
            return ! empty($extracted['aadhaar']) && ! empty($extracted['name']);
        }
        if ($detected === 'salary_slip') {
            return ! empty($extracted['net_salary']) || ! empty($extracted['gross_salary']);
        }
        if ($detected === 'bank_statement') {
            return ! empty($extracted['bank_name']) || ! empty($extracted['account_holder_name']);
        }
        return false;
    }

    private function guessHeaderName(string $text): ?string
    {
        $lines = OcrText::lines($text);
        foreach (array_slice($lines, 0, 5) as $line) {
            $u = OcrText::upper($line);
            if (strlen($line) >= 3 && ! str_contains($u, 'PAY') && ! str_contains($u, 'SLIP') && ! str_contains($u, 'STATEMENT')) {
                return $line;
            }
        }
        return null;
    }

    private function extractBankName(string $text): ?string
    {
        $lines = OcrText::lines($text);
        foreach (array_slice($lines, 0, 12) as $line) {
            $u = OcrText::upper($line);
            if (str_contains($u, 'BANK') && strlen($line) <= 60) {
                return $line;
            }
        }
        return null;
    }

    private function money(mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'Not Found';
        }
        if (is_numeric($value)) {
            return 'INR '.number_format((float) $value, 2, '.', ',');
        }
        return 'Uncertain';
    }
}
