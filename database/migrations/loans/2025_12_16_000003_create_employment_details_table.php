<?php

/**
 * Migration: Create the `employment_details` table.
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
        Schema::create('employment_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('applicant_id');
            
            // Employment Type
            $table->enum('employment_type', [
                'SALARIED',
                'SELF_EMPLOYED_PROFESSIONAL',
                'SELF_EMPLOYED_BUSINESS',
                'RETIRED',
                'UNEMPLOYED',
                'STUDENT',
                'HOMEMAKER'
            ]);
            
            $table->enum('employment_status', [
                'CURRENT',
                'PREVIOUS'
            ])->default('CURRENT');
            
            // Company/Business Details
            $table->string('company_name', 200);
            $table->string('company_type', 100)->nullable(); // Private Ltd, Public Ltd, Partnership, Proprietorship
            $table->string('industry_type', 100)->nullable(); // IT, Banking, Manufacturing, etc.
            $table->string('industry_code', 20)->nullable(); // NIC Code
            $table->string('company_pan', 10)->nullable();
            $table->string('company_gstin', 15)->nullable();
            $table->date('company_incorporation_date')->nullable();
            
            // Job Details (for Salaried)
            $table->string('designation', 100)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('employee_id', 50)->nullable();
            $table->date('date_of_joining')->nullable();
            $table->date('date_of_leaving')->nullable();
            
            // Experience
            $table->integer('total_experience_years')->nullable();
            $table->integer('total_experience_months')->nullable();
            $table->integer('current_company_experience_years')->nullable();
            $table->integer('current_company_experience_months')->nullable();
            
            // Business Details (for Self-Employed)
            $table->string('business_nature', 200)->nullable();
            $table->integer('years_in_business')->nullable();
            $table->string('office_ownership', 50)->nullable(); // OWNED, RENTED, LEASED
            
            // Work Contact
            $table->string('office_phone', 15)->nullable();
            $table->string('office_email', 100)->nullable();
            $table->string('reporting_manager_name', 200)->nullable();
            $table->string('reporting_manager_contact', 15)->nullable();
            $table->string('hr_contact_name', 200)->nullable();
            $table->string('hr_contact_phone', 15)->nullable();
            
            // Verification
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->enum('verification_method', [
                'PHONE',
                'EMAIL',
                'PHYSICAL_VISIT',
                'DOCUMENT',
                'NOT_VERIFIED'
            ])->default('NOT_VERIFIED');
            $table->text('verification_notes')->nullable();
            
            // Document References
            $table->string('appointment_letter_path', 500)->nullable();
            $table->string('experience_letter_path', 500)->nullable();
            $table->string('business_registration_path', 500)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('applicant_id')
                  ->references('id')
                  ->on('applicants')
                  ->onDelete('cascade');
            
            $table->foreign('verified_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            // Indexes
            $table->index(['applicant_id', 'employment_status'], 'idx_applicant_employment');
            $table->index('employment_type', 'idx_employment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_details');
    }
};
