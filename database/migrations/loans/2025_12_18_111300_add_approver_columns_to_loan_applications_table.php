<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('loan_applications', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
                $table->index('approved_by', 'idx_loan_applications_approved_by');
            }

            if (! Schema::hasColumn('loan_applications', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
                $table->index('rejected_by', 'idx_loan_applications_rejected_by');
            }
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            // Add FKs after columns exist (safe on existing installs)
            if (Schema::hasColumn('loan_applications', 'approved_by')) {
                $table->foreign('approved_by', 'fk_loan_applications_approved_by')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }

            if (Schema::hasColumn('loan_applications', 'rejected_by')) {
                $table->foreign('rejected_by', 'fk_loan_applications_rejected_by')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            if (Schema::hasColumn('loan_applications', 'approved_by')) {
                $table->dropForeign('fk_loan_applications_approved_by');
                $table->dropIndex('idx_loan_applications_approved_by');
                $table->dropColumn('approved_by');
            }

            if (Schema::hasColumn('loan_applications', 'rejected_by')) {
                $table->dropForeign('fk_loan_applications_rejected_by');
                $table->dropIndex('idx_loan_applications_rejected_by');
                $table->dropColumn('rejected_by');
            }
        });
    }
};
