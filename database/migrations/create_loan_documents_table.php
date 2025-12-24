<?php

/**
 * Migration: Create/patch the `loan_documents` table for uploaded loan documents.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('loan_documents')) {
            Schema::table('loan_documents', function (Blueprint $table) {
                if (! Schema::hasColumn('loan_documents', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->after('loan_application_id');
                }

                if (! Schema::hasColumn('loan_documents', 'original_name')) {
                    $table->string('original_name', 255)->after('file_path')->default('');
                }

                if (! Schema::hasColumn('loan_documents', 'verified_by')) {
                    $table->unsignedBigInteger('verified_by')->nullable()->after('original_name');
                }

                if (! Schema::hasColumn('loan_documents', 'verified_at')) {
                    $table->timestamp('verified_at')->nullable()->after('verified_by');
                }
            });

            return;
        }

        Schema::create('loan_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('loan_application_id');
            $table->unsignedBigInteger('user_id');

            $table->string('document_type', 50);
            $table->string('file_path', 255);
            $table->string('original_name', 255);

            // Optional: supports internal verification flows already present in the app
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->unique(['loan_application_id', 'document_type']);

            $table
                ->foreign('loan_application_id')
                ->references('id')
                ->on('loan_applications')
                ->onDelete('cascade');

            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table
                ->foreign('verified_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_documents');
    }
};
