<?php

namespace App\Services\DocumentVerification;

final class Parsers
{
    public static function extractPanNumber(string $text): ?string
    {
        $t = OcrText::upper($text);
        if (preg_match('/\b([A-Z]{5}[0-9]{4}[A-Z])\b/', $t, $m)) {
            return $m[1];
        }
        return null;
    }

    public static function extractAadhaar(string $text): ?string
    {
        $t = OcrText::upper($text);

        if (preg_match('/\b(X{4}\s?X{4}\s?\d{4})\b/', $t, $m)) {
            return preg_replace('/\s+/', '-', $m[1]) ?? $m[1];
        }

        if (preg_match('/\b(\d{4})\s?(\d{4})\s?(\d{4})\b/', $t, $m)) {
            return $m[1].'-'.$m[2].'-'.$m[3];
        }

        // Sometimes OCR outputs continuous 12 digits
        if (preg_match('/\b(\d{12})\b/', $t, $m)) {
            return substr($m[1], 0, 4).'-'.substr($m[1], 4, 4).'-'.substr($m[1], 8, 4);
        }

        return null;
    }

    public static function isAadhaarMaskingValid(?string $aadhaar): bool
    {
        if ($aadhaar === null || $aadhaar === '') {
            return false;
        }

        $t = OcrText::upper($aadhaar);
        if (preg_match('/^\d{4}-\d{4}-\d{4}$/', $t)) {
            return true;
        }

        return (bool) preg_match('/^X{4}-X{4}-\d{4}$/', $t);
    }

    public static function extractDob(string $text): ?string
    {
        $t = OcrText::normalize($text);

        // dd/mm/yyyy or dd-mm-yyyy
        if (preg_match('/\b(\d{2})[\/-](\d{2})[\/-](\d{4})\b/', $t, $m)) {
            return $m[1].'/'.$m[2].'/'.$m[3];
        }

        // yyyy-mm-dd
        if (preg_match('/\b(\d{4})[\/-](\d{2})[\/-](\d{2})\b/', $t, $m)) {
            return $m[3].'/'.$m[2].'/'.$m[1];
        }

        return null;
    }

    public static function extractYearOfBirth(string $text): ?string
    {
        $t = OcrText::upper($text);
        if (preg_match('/\b(YOB|YEAR OF BIRTH)\s*[:\-]?\s*(\d{4})\b/', $t, $m)) {
            return $m[2];
        }
        return null;
    }

    public static function extractLabeledValue(string $text, string $label): ?string
    {
        $lines = OcrText::lines($text);
        $labelU = OcrText::upper($label);

        foreach ($lines as $idx => $line) {
            $u = OcrText::upper($line);
            if (str_contains($u, $labelU)) {
                // Inline value like "NAME: JOHN DOE"
                $parts = preg_split('/[:\-]/', $line, 2);
                if ($parts && count($parts) === 2) {
                    $v = trim($parts[1]);
                    if ($v !== '') {
                        return $v;
                    }
                }

                // Otherwise take next non-empty line
                $next = $lines[$idx + 1] ?? null;
                if ($next !== null && trim($next) !== '') {
                    return trim($next);
                }
            }
        }

        return null;
    }

    public static function extractMoney(string $text, array $labels): ?float
    {
        $t = OcrText::upper($text);
        foreach ($labels as $label) {
            $labelU = OcrText::upper($label);
            if (preg_match('/'.preg_quote($labelU, '/').'\s*[:\-]?\s*(?:INR|RS\.?|₹)?\s*([0-9][0-9,]*\.?[0-9]{0,2})\b/', $t, $m)) {
                $n = str_replace(',', '', $m[1]);
                if (is_numeric($n)) {
                    return (float) $n;
                }
            }
        }

        // Fallback: not label-based, do not guess.
        return null;
    }

    public static function extractMonthYear(string $text): ?string
    {
        $t = OcrText::upper($text);

        // Explicit patterns
        if (preg_match('/\b(PAY\s*PERIOD|PAYSLIP\s*FOR|MONTH)\s*[:\-]?\s*([A-Z]{3,9})\s*(\d{4})\b/', $t, $m)) {
            return ucfirst(strtolower($m[2])).' '.$m[3];
        }

        // Generic month-year (conservative: require a common month word)
        $months = '(JANUARY|FEBRUARY|MARCH|APRIL|MAY|JUNE|JULY|AUGUST|SEPTEMBER|OCTOBER|NOVEMBER|DECEMBER|JAN|FEB|MAR|APR|JUN|JUL|AUG|SEP|SEPT|OCT|NOV|DEC)';
        if (preg_match('/\b'.$months.'\b\s*(\d{4})\b/', $t, $m)) {
            $month = ucfirst(strtolower($m[0]));
            $year = $m[count($m) - 1];
            return $month.' '.$year;
        }

        return null;
    }
}
