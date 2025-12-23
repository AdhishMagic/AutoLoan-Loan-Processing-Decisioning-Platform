<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_documents', function (Blueprint $table) {
            if (! Schema::hasColumn('loan_documents', 'ocr_text')) {
                $table->longText('ocr_text')->nullable()->after('original_name');
            }

            if (! Schema::hasColumn('loan_documents', 'ocr_normalized_text')) {
                $table->longText('ocr_normalized_text')->nullable()->after('ocr_text');
            }

            if (! Schema::hasColumn('loan_documents', 'ocr_hash')) {
                $table->string('ocr_hash', 64)->nullable()->after('ocr_normalized_text');
            }

            if (! Schema::hasColumn('loan_documents', 'extracted_data')) {
                $table->json('extracted_data')->nullable()->after('ocr_hash');
            }

            if (! Schema::hasColumn('loan_documents', 'verification_result')) {
                $table->json('verification_result')->nullable()->after('extracted_data');
            }

            if (! Schema::hasColumn('loan_documents', 'authenticity_score')) {
                $table->unsignedTinyInteger('authenticity_score')->nullable()->after('verification_result');
            }

            if (! Schema::hasColumn('loan_documents', 'uniqueness_score')) {
                $table->unsignedTinyInteger('uniqueness_score')->nullable()->after('authenticity_score');
            }

            if (! Schema::hasColumn('loan_documents', 'trust_score')) {
                $table->unsignedTinyInteger('trust_score')->nullable()->after('uniqueness_score');
            }

            if (! Schema::hasColumn('loan_documents', 'analyzed_at')) {
                $table->timestamp('analyzed_at')->nullable()->after('trust_score');
            }

            if (! Schema::hasColumn('loan_documents', 'analysis_version')) {
                $table->string('analysis_version', 20)->nullable()->after('analyzed_at');
            }

            // Best-effort: index may already exist in some environments
            try {
                $table->index(['loan_application_id', 'document_type']);
            } catch (Throwable $e) {
                // ignore
            }

            try {
                $table->index(['loan_application_id', 'ocr_hash']);
            } catch (Throwable $e) {
                // ignore
            }
        });
    }

    public function down(): void
    {
        Schema::table('loan_documents', function (Blueprint $table) {
            foreach ([
                'analysis_version',
                'analyzed_at',
                'trust_score',
                'uniqueness_score',
                'authenticity_score',
                'verification_result',
                'extracted_data',
                'ocr_hash',
                'ocr_normalized_text',
                'ocr_text',
            ] as $column) {
                if (Schema::hasColumn('loan_documents', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Best-effort: indexes may not exist in all environments
            try {
                $table->dropIndex(['loan_application_id', 'document_type']);
            } catch (Throwable $e) {
                // ignore
            }

            try {
                $table->dropIndex(['loan_application_id', 'ocr_hash']);
            } catch (Throwable $e) {
                // ignore
            }
        });
    }
};
