<?php

/**
 * Migration: Add OCR and verification fields to the `loan_documents` table.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No-op.
        // This migration was added to move OCR/verification columns into the root migrations directory,
        // but the earlier migration (2025_12_23_000001_...) already executed successfully.
        // Keeping this file as a no-op ensures `php artisan migrate` can complete cleanly.
    }

    public function down(): void
    {
        // No-op.
    }
};
