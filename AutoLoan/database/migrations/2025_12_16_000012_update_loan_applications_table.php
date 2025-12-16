<?php

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
        // Drop existing foreign keys first
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['assigned_officer_id']);
            $table->dropIndex('idx_loan_status');
            $table->dropIndex('idx_loan_user');
        });
        
        // Rename existing table as backup
        Schema::rename('loan_applications', 'loan_applications_old');
        
        // Create new loan_applications table with UUID
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Application Tracking
            $table->string('application_number', 50)->unique()->index();
            $table->date('application_date');
            
            // Primary Applicant Reference (for convenience)
            $table->unsignedBigInteger('user_id')->nullable(); // Auth user who initiated
            
            // Loan Product Details
            $table->enum('loan_product_type', [
                'LAP',      // Loan Against Property
                'LCP',      // Loan for Construction of Property
                'DOD',      // Debt on Demand
                'LARR',     // Loan Against Rent Receivables
                'HOME_LOAN',
                'MORTGAGE_LOAN',
                'BUSINESS_LOAN',
                'PERSONAL_LOAN',
                'OTHER'
            ]);
            
            $table->string('loan_product_code', 50)->nullable();
            $table->string('loan_scheme', 100)->nullable();
            
            // Loan Amount & Tenure
            $table->decimal('requested_amount', 14, 2);
            $table->decimal('sanctioned_amount', 14, 2)->nullable();
            $table->decimal('disbursed_amount', 14, 2)->nullable();
            
            $table->integer('requested_tenure_months');
            $table->integer('sanctioned_tenure_months')->nullable();
            
            // Interest Details
            $table->decimal('requested_interest_rate', 5, 2)->nullable();
            $table->decimal('sanctioned_interest_rate', 5, 2)->nullable();
            $table->enum('interest_type', [
                'FIXED',
                'FLOATING',
                'VARIABLE',
                'HYBRID'
            ])->default('FLOATING');
            
            $table->decimal('processing_fee', 12, 2)->nullable();
            $table->decimal('processing_fee_percentage', 5, 2)->nullable();
            
            // EMI Details
            $table->decimal('emi_amount', 12, 2)->nullable();
            $table->integer('emi_date')->nullable(); // Day of month (1-31)
            
            // End Use of Loan
            $table->enum('loan_purpose', [
                'PURCHASE_PROPERTY',
                'CONSTRUCTION',
                'RENOVATION',
                'EXTENSION',
                'BUSINESS_EXPANSION',
                'WORKING_CAPITAL',
                'DEBT_CONSOLIDATION',
                'MEDICAL_EMERGENCY',
                'EDUCATION',
                'WEDDING',
                'OTHER'
            ]);
            
            $table->text('purpose_description')->nullable();
            
            // Status & Stage
            $table->enum('status', [
                'DRAFT',
                'SUBMITTED',
                'UNDER_REVIEW',
                'KYC_PENDING',
                'KYC_IN_PROGRESS',
                'KYC_COMPLETED',
                'DOCUMENT_PENDING',
                'DOCUMENT_VERIFICATION',
                'CREDIT_CHECK_PENDING',
                'CREDIT_CHECK_IN_PROGRESS',
                'CREDIT_CHECK_COMPLETED',
                'TECHNICAL_VERIFICATION',
                'LEGAL_VERIFICATION',
                'VALUATION_PENDING',
                'VALUATION_IN_PROGRESS',
                'VALUATION_COMPLETED',
                'UNDERWRITING',
                'QUERY_RAISED',
                'PENDING_APPROVAL',
                'APPROVED',
                'CONDITIONALLY_APPROVED',
                'REJECTED',
                'SANCTIONED',
                'DISBURSED',
                'CANCELLED',
                'WITHDRAWN',
                'ON_HOLD'
            ])->default('DRAFT')->index();
            
            $table->string('current_stage', 100)->nullable();
            $table->integer('stage_order')->default(1);
            
            // Assignment
            $table->unsignedBigInteger('assigned_officer_id')->nullable();
            $table->unsignedBigInteger('credit_manager_id')->nullable();
            $table->unsignedBigInteger('underwriter_id')->nullable();
            $table->string('assigned_branch', 100)->nullable();
            
            // Important Dates
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('kyc_completed_at')->nullable();
            $table->timestamp('documents_completed_at')->nullable();
            $table->timestamp('credit_check_completed_at')->nullable();
            $table->timestamp('technical_verification_at')->nullable();
            $table->timestamp('legal_verification_at')->nullable();
            $table->timestamp('valuation_completed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('sanctioned_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            
            // Rejection/Cancellation
            $table->text('rejection_reason')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            
            // Financial Assessment
            $table->decimal('monthly_income', 12, 2)->nullable();
            $table->decimal('monthly_obligations', 12, 2)->nullable();
            $table->decimal('foir', 5, 2)->nullable(); // Fixed Obligation to Income Ratio
            $table->decimal('ltv_ratio', 5, 2)->nullable(); // Loan to Value Ratio
            $table->decimal('dscr', 5, 2)->nullable(); // Debt Service Coverage Ratio
            
            // Credit Score
            $table->integer('cibil_score')->nullable();
            $table->string('credit_bureau', 50)->nullable();
            $table->date('credit_report_date')->nullable();
            
            // Risk Assessment
            $table->enum('risk_category', [
                'LOW',
                'MEDIUM',
                'HIGH',
                'VERY_HIGH'
            ])->nullable();
            
            $table->integer('risk_score')->nullable();
            
            // Disbursement Details
            $table->string('loan_account_number', 50)->nullable()->unique();
            $table->uuid('disbursement_bank_account_id')->nullable();
            $table->enum('disbursement_mode', [
                'NEFT',
                'RTGS',
                'IMPS',
                'CHEQUE',
                'DD',
                'DIRECT_PAYMENT'
            ])->nullable();
            
            $table->string('disbursement_reference', 100)->nullable();
            $table->text('disbursement_remarks')->nullable();
            
            // Communication Preferences
            $table->enum('preferred_communication', [
                'EMAIL',
                'SMS',
                'PHONE',
                'WHATSAPP',
                'ALL'
            ])->default('ALL');
            
            // Priority & SLA
            $table->enum('priority', [
                'LOW',
                'NORMAL',
                'HIGH',
                'URGENT'
            ])->default('NORMAL');
            
            $table->timestamp('sla_deadline')->nullable();
            $table->boolean('is_sla_breached')->default(false);
            
            // Compliance & Flags
            $table->boolean('is_high_value')->default(false);
            $table->boolean('requires_manager_approval')->default(false);
            $table->boolean('is_fast_track')->default(false);
            $table->boolean('is_top_up_loan')->default(false);
            $table->uuid('parent_loan_id')->nullable(); // For top-up loans
            
            // Notes & Remarks
            $table->text('internal_notes')->nullable();
            $table->text('customer_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            $table->foreign('assigned_officer_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            $table->foreign('credit_manager_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            $table->foreign('underwriter_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            $table->foreign('rejected_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            $table->foreign('approved_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            // Indexes for Performance
            $table->index('application_number', 'idx_application_number');
            $table->index('status', 'idx_status');
            $table->index('current_stage', 'idx_current_stage');
            $table->index(['status', 'assigned_officer_id'], 'idx_status_officer');
            $table->index(['loan_product_type', 'status'], 'idx_product_status');
            $table->index('application_date', 'idx_application_date');
            $table->index(['submitted_at', 'status'], 'idx_submitted_status');
            $table->index('risk_category', 'idx_risk_category');
            $table->index('is_sla_breached', 'idx_sla_breach');
        });
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
