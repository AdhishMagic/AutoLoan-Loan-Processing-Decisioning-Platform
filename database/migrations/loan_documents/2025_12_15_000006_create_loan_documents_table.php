<?php

/**
 * Migration: Create the `loan_documents` table for uploaded loan documents.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No-op.
        // Canonical loan_documents migration lives in:
        // database/migrations/create_loan_documents_table.php
        // Keeping this file as a no-op avoids duplicate table creation.
    }

    public function down(): void
    {
        // No-op.
    }
};
