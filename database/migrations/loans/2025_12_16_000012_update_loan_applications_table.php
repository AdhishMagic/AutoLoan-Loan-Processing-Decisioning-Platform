<?php

/**
 * Migration: Update/extend the `loan_applications` table schema.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration updates the existing loan_applications table to support
     * the comprehensive loan application system with UUID primary keys
     */
    public function up(): void
    {
        // No-op.
        // Canonical loan_applications migration lives in:
        // database/migrations/loans/2025_12_15_000005_create_loan_applications_table.php
        // Keeping this file as a no-op preserves migration history and avoids unsafe
        // drop/rename behavior on rollback.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op.
    }
};
