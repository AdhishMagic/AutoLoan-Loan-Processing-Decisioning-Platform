<?php

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
        Schema::create('applicants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_application_id');
            $table->unsignedBigInteger('user_id')->nullable(); // Link to auth user if registered
            
            // Role & Type
            $table->enum('applicant_role', ['PRIMARY', 'CO_APPLICANT'])->default('PRIMARY');
            
            // Personal Details
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->date('date_of_birth');
            $table->enum('gender', ['MALE', 'FEMALE', 'OTHER']);
            $table->enum('marital_status', ['SINGLE', 'MARRIED', 'DIVORCED', 'WIDOWED']);
            $table->string('father_name', 200)->nullable();
            $table->string('mother_name', 200)->nullable();
            $table->string('spouse_name', 200)->nullable();
            
            // Contact Details
            $table->string('mobile', 15)->index();
            $table->string('alternate_mobile', 15)->nullable();
            $table->string('email', 100)->index();
            $table->string('alternate_email', 100)->nullable();
            
            // Identity & Demographics
            $table->string('pan_number', 10)->unique()->index();
            $table->string('aadhaar_number', 12)->unique()->index();
            $table->string('passport_number', 20)->nullable();
            $table->string('voter_id', 20)->nullable();
            $table->string('driving_license', 20)->nullable();
            
            $table->string('religion', 50)->nullable();
            $table->string('category', 50)->nullable(); // General, OBC, SC, ST
            $table->string('nationality', 50)->default('INDIAN');
            
            // Education & Professional
            $table->enum('education_level', [
                'HIGH_SCHOOL',
                'DIPLOMA',
                'GRADUATE',
                'POST_GRADUATE',
                'DOCTORATE',
                'PROFESSIONAL'
            ])->nullable();
            
            // Residential Status
            $table->enum('residential_status', [
                'OWNED',
                'RENTED',
                'PARENTAL',
                'COMPANY_PROVIDED',
                'OTHER'
            ])->nullable();
            $table->integer('years_at_current_residence')->nullable();
            
            // Dependents
            $table->integer('number_of_dependents')->default(0);
            
            // KYC Status
            $table->enum('kyc_status', [
                'PENDING',
                'IN_PROGRESS',
                'VERIFIED',
                'REJECTED'
            ])->default('PENDING');
            $table->string('kyc_reference_number', 100)->nullable()->unique();
            $table->timestamp('kyc_verified_at')->nullable();
            $table->unsignedBigInteger('kyc_verified_by')->nullable();
            
            // Meta
            $table->boolean('is_politically_exposed')->default(false);
            $table->text('additional_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('loan_application_id')
                  ->references('id')
                  ->on('loan_applications')
                  ->onDelete('cascade');
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            $table->foreign('kyc_verified_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            // Indexes
            $table->index(['loan_application_id', 'applicant_role'], 'idx_loan_applicant_role');
            $table->index('kyc_status', 'idx_kyc_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
