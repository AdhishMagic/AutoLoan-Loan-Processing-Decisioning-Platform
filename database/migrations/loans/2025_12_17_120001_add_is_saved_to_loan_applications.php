<?php

/**
 * Migration: Add the `is_saved` flag to the `loan_applications` table (draft/resume UX).
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->boolean('is_saved')->default(false)->after('status');
            $table->index(['status', 'is_saved'], 'idx_status_saved');
        });
    }

    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropIndex('idx_status_saved');
            $table->dropColumn('is_saved');
        });
    }
};
