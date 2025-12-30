<?php

/**
 * Migration: Update unique indexes on the `applicants` table.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Attempt to drop any existing unique constraints on aadhaar_number and pan_number regardless of name (PostgreSQL)
        if (DB::getDriverName() === 'pgsql') {
            DB::unprepared(<<<'SQL'
DO $$
DECLARE r RECORD;
BEGIN
    FOR r IN (
        SELECT conname
        FROM pg_constraint c
        JOIN pg_class t ON t.oid = c.conrelid
        WHERE t.relname = 'applicants'
            AND c.contype = 'u'
            AND (pg_get_constraintdef(c.oid) ILIKE '%(aadhaar_number%' OR pg_get_constraintdef(c.oid) ILIKE '%(pan_number%')
    ) LOOP
        EXECUTE format('ALTER TABLE applicants DROP CONSTRAINT IF EXISTS %I', r.conname);
    END LOOP;
END$$;
SQL);
        }

        Schema::table('applicants', function (Blueprint $table) {
            // Add composite unique per loan to avoid duplicates within a loan
            $table->unique(['loan_application_id', 'aadhaar_number'], 'applicant_loan_aadhaar_unique');
            $table->unique(['loan_application_id', 'pan_number'], 'applicant_loan_pan_unique');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            // Drop composite uniques
            try { $table->dropUnique('applicant_loan_aadhaar_unique'); } catch (\Throwable $e) {}
            try { $table->dropUnique('applicant_loan_pan_unique'); } catch (\Throwable $e) {}

            // Restore global uniques
            $table->unique('aadhaar_number');
            $table->unique('pan_number');
        });
    }
};
