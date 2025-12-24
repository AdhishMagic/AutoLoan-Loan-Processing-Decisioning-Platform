<?php

/**
 * Migration: Create the `loan_declarations` table.
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
        Schema::create('loan_declarations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_application_id');
            $table->uuid('applicant_id')->nullable(); // If specific to an applicant
            
            // Declaration Type
            $table->enum('declaration_type', [
                'INCOME_DECLARATION',
                'EMPLOYMENT_DECLARATION',
                'PROPERTY_DECLARATION',
                'NO_LITIGATION_DECLARATION',
                'CRIMINAL_RECORD_DECLARATION',
                'BANKRUPTCY_DECLARATION',
                'WILLFUL_DEFAULT_DECLARATION',
                'CONSENT_DECLARATION',
                'CIBIL_CONSENT',
                'DATA_SHARING_CONSENT',
                'MARKETING_CONSENT',
                'TERMS_CONDITIONS',
                'GENERAL_DECLARATION',
                'OTHER'
            ]);
            
            // Declaration Content
            $table->string('declaration_title', 200);
            $table->text('declaration_text');
            $table->text('declaration_points')->nullable(); // JSON array of points
            
            // Acceptance
            $table->boolean('is_accepted')->default(false);
            $table->timestamp('accepted_at')->nullable();
            $table->ipAddress('accepted_ip')->nullable();
            $table->string('accepted_user_agent', 500)->nullable();
            $table->string('accepted_device_info', 200)->nullable();
            
            // Digital Signature
            $table->string('digital_signature_hash', 500)->nullable();
            $table->string('signature_image_path', 500)->nullable();
            
            // Version Control
            $table->string('declaration_version', 20)->default('1.0');
            $table->boolean('is_mandatory')->default(true);
            $table->integer('display_order')->default(1);
            
            // Witness (if applicable)
            $table->string('witness_name', 200)->nullable();
            $table->string('witness_signature_path', 500)->nullable();
            
            // Validity
            $table->date('valid_from')->nullable();
            $table->date('valid_till')->nullable();
            
            // Audit
            $table->text('remarks')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('loan_application_id')
                  ->references('id')
                  ->on('loan_applications')
                  ->onDelete('cascade');
            
            $table->foreign('applicant_id')
                  ->references('id')
                  ->on('applicants')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index(['loan_application_id', 'declaration_type'], 'idx_loan_declaration_type');
            $table->index(['applicant_id', 'is_accepted'], 'idx_applicant_acceptance');
            $table->index('is_mandatory', 'idx_mandatory_declaration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_declarations');
    }
};
