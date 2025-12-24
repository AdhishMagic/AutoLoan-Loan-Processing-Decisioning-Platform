<?php

/**
 * Migration: Create the `loan_references` table.
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
        Schema::create('loan_references', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_application_id');
            
            // Reference Type
            $table->enum('reference_type', [
                'PERSONAL',
                'PROFESSIONAL',
                'RELATIVE',
                'FAMILY_FRIEND',
                'COLLEAGUE',
                'NEIGHBOR',
                'OTHER'
            ]);
            
            // Reference Details
            $table->string('full_name', 200);
            $table->enum('relationship', [
                'FATHER',
                'MOTHER',
                'BROTHER',
                'SISTER',
                'SPOUSE',
                'FRIEND',
                'COLLEAGUE',
                'MANAGER',
                'NEIGHBOR',
                'BUSINESS_ASSOCIATE',
                'OTHER'
            ])->nullable();
            
            $table->string('mobile', 15)->index();
            $table->string('alternate_mobile', 15)->nullable();
            $table->string('email', 100)->nullable();
            
            // Address
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 10)->nullable();
            
            // Professional Details
            $table->string('occupation', 100)->nullable();
            $table->string('company_name', 200)->nullable();
            $table->string('designation', 100)->nullable();
            
            // Know Since
            $table->integer('known_since_years')->nullable();
            $table->string('how_do_you_know', 200)->nullable();
            
            // Verification
            $table->enum('verification_status', [
                'PENDING',
                'CONTACTED',
                'VERIFIED',
                'UNREACHABLE',
                'DECLINED',
                'FAILED'
            ])->default('PENDING');
            
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->enum('verification_method', [
                'PHONE',
                'EMAIL',
                'PHYSICAL_VISIT',
                'NOT_VERIFIED'
            ])->default('NOT_VERIFIED');
            
            $table->text('verification_notes')->nullable();
            $table->text('reference_feedback')->nullable();
            $table->enum('feedback_rating', [
                'POSITIVE',
                'NEUTRAL',
                'NEGATIVE'
            ])->nullable();
            
            // Priority
            $table->integer('priority_order')->default(1);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('loan_application_id')
                  ->references('id')
                  ->on('loan_applications')
                  ->onDelete('cascade');
            
            $table->foreign('verified_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            // Indexes
            $table->index('loan_application_id', 'idx_loan_reference');
            $table->index('verification_status', 'idx_reference_verification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_references');
    }
};
