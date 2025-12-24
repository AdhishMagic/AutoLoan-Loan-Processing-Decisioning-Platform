<?php

/**
 * Migration: Create the `notifications` table (Laravel notification system).
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // No-op.
        // Canonical notifications migration lives in:
        // database/migrations/users/2025_12_15_000012_create_notifications_table.php
        // Keeping this legacy migration as a no-op avoids duplicate table creation
        // while preserving migration history for existing environments.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op.
        // The canonical migration is responsible for creating/dropping the table.
    }
};
