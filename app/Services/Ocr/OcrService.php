<?php

namespace App\Services\Ocr;

use App\Services\DocumentVerification\OcrText;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class OcrService
{
    /**
     * Extract best-effort text from a local file path.
     *
     * This intentionally relies on system binaries when available:
     * - PDFs: `pdftotext`
     * - Images: `tesseract`
     */
    public function extractText(string $absolutePath): ?string
    {
        if (! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return null;
        }

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        try {
            if ($ext === 'pdf') {
                $text = $this->extractFromPdf($absolutePath);
                return $text !== null ? OcrText::normalize($text) : null;
            }

            if (in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
                $text = $this->extractFromImage($absolutePath);
                return $text !== null ? OcrText::normalize($text) : null;
            }
        } catch (\Throwable $e) {
            Log::warning('OCR extraction failed', [
                'path' => $absolutePath,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    private function extractFromPdf(string $absolutePath): ?string
    {
        // Prefer system poppler when available.
        if ($this->binaryExists('pdftotext')) {
            // pdftotext <pdf> - (write to stdout)
            $process = new Process(['pdftotext', $absolutePath, '-']);
            $process->setTimeout(60);
            $process->run();

            if (! $process->isSuccessful()) {
                Log::info('OCR: pdftotext failed', ['exit_code' => $process->getExitCode()]);
                return null;
            }

            $out = trim((string) $process->getOutput());
            return $out !== '' ? $out : null;
        }

        // Fallback for digital/text PDFs when system binaries are not installed.
        // Note: this does NOT OCR scanned PDFs; it only extracts embedded text.
        try {
            if (class_exists(\Smalot\PdfParser\Parser::class)) {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($absolutePath);
                $text = trim((string) $pdf->getText());

                if ($text !== '') {
                    return $text;
                }

                Log::info('OCR: PDF parser extracted empty text (likely scanned PDF).');
                return null;
            }
        } catch (\Throwable $e) {
            Log::info('OCR: PDF parser failed', ['error' => $e->getMessage()]);
        }

        Log::info('OCR: pdftotext not available; skipping PDF text extraction.');
        return null;
    }

    private function extractFromImage(string $absolutePath): ?string
    {
        if (! $this->binaryExists('tesseract')) {
            Log::info('OCR: tesseract not available; skipping image OCR.');
            return null;
        }

        // tesseract <img> stdout
        $process = new Process(['tesseract', $absolutePath, 'stdout']);
        $process->setTimeout(120);
        $process->run();

        if (! $process->isSuccessful()) {
            Log::info('OCR: tesseract failed', ['exit_code' => $process->getExitCode()]);
            return null;
        }

        $out = trim((string) $process->getOutput());
        return $out !== '' ? $out : null;
    }

    private function binaryExists(string $binary): bool
    {
        $process = new Process(['which', $binary]);
        $process->setTimeout(5);
        $process->run();

        return $process->isSuccessful() && trim((string) $process->getOutput()) !== '';
    }
}
