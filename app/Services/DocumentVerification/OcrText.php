<?php

namespace App\Services\DocumentVerification;

final class OcrText
{
    public static function normalize(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[\t\x0B\f\xC2\xA0]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/[ ]{2,}/', ' ', $text) ?? $text;
        $text = preg_replace('/\n{3,}/', "\n\n", $text) ?? $text;

        // Common OCR confusions (best-effort, conservative)
        // Only apply for alphanumeric-heavy contexts; keep it light here.
        $text = str_replace(['—', '–'], '-', $text);

        return trim($text);
    }

    public static function upper(string $text): string
    {
        return mb_strtoupper($text, 'UTF-8');
    }

    public static function sha256(string $normalizedText): string
    {
        return hash('sha256', $normalizedText);
    }

    /**
     * @return array<int, string>
     */
    public static function lines(string $text): array
    {
        $text = self::normalize($text);
        $lines = preg_split('/\n/', $text) ?: [];
        $lines = array_map(fn ($l) => trim((string) $l), $lines);
        return array_values(array_filter($lines, fn ($l) => $l !== ''));
    }
}
