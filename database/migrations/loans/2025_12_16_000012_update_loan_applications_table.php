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
        // No-op: initial create migration now uses UUID. This update is skipped.
        if (! Schema::hasTable('loan_applications')) {
            Schema::create('loan_applications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->timestamps();
            });
        }
        // If the table already exists (created by earlier migration), skip updates to avoid duplicates.
        if (Schema::hasTable('loan_applications')) {
            return;
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
        Schema::rename('loan_applications_old', 'loan_applications');
        
        // Restore original indexes
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->index('status', 'idx_loan_status');
            $table->index('user_id', 'idx_loan_user');
        });
    }
};
