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
        Schema::create('existing_loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('applicant_id');
            
            // Loan Type
            $table->enum('loan_type', [
                'HOME_LOAN',
                'PERSONAL_LOAN',
                'AUTO_LOAN',
                'EDUCATION_LOAN',
                'GOLD_LOAN',
                'LAP', // Loan Against Property
                'BUSINESS_LOAN',
                'CREDIT_CARD_EMI',
                'OVERDRAFT',
                'OTHER'
            ]);
            
            // Lender Details
            $table->string('lender_name', 200);
            $table->enum('lender_type', [
                'BANK',
                'NBFC',
                'HOUSING_FINANCE',
                'COOPERATIVE',
                'PRIVATE_LENDER',
                'OTHER'
            ])->nullable();
            
            $table->string('loan_account_number', 50)->nullable();
            
            // Loan Details
            $table->decimal('original_loan_amount', 14, 2);
            $table->decimal('current_outstanding', 14, 2);
            $table->decimal('emi_amount', 12, 2);
            $table->integer('total_tenure_months');
            $table->integer('remaining_tenure_months');
            $table->decimal('interest_rate', 5, 2);
            $table->enum('interest_type', [
                'FIXED',
                'FLOATING',
                'VARIABLE'
            ])->nullable();
            
            // Dates
            $table->date('loan_disbursement_date')->nullable();
            $table->date('loan_maturity_date')->nullable();
            $table->date('last_emi_date')->nullable();
            $table->date('next_emi_date')->nullable();
            
            // Payment Behavior
            $table->enum('repayment_status', [
                'REGULAR',
                'IRREGULAR',
                'DEFAULTED',
                'CLOSED',
                'SETTLED',
                'WRITTEN_OFF'
            ])->default('REGULAR');
            
            $table->integer('dpd_days')->default(0); // Days Past Due
            $table->integer('bounced_emi_count')->default(0);
            $table->integer('missed_emi_count')->default(0);
            $table->boolean('has_overdue')->default(false);
            
            // Security/Collateral
            $table->enum('loan_security_type', [
                'SECURED',
                'UNSECURED'
            ])->default('UNSECURED');
            $table->string('collateral_description', 500)->nullable();
            
            // Closure Details
            $table->boolean('is_to_be_closed')->default(false);
            $table->decimal('preclosure_amount', 14, 2)->nullable();
            $table->boolean('is_considered_for_obligation')->default(true);
            
            // Verification
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->enum('verification_method', [
                'CREDIT_REPORT',
                'BANK_STATEMENT',
                'LOAN_STATEMENT',
                'NOC',
                'NOT_VERIFIED'
            ])->default('NOT_VERIFIED');
            $table->text('verification_notes')->nullable();
            
            // Documents
            $table->string('loan_statement_path', 500)->nullable();
            $table->string('sanction_letter_path', 500)->nullable();
            $table->string('noc_path', 500)->nullable();
            
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
            $table->index(['applicant_id', 'loan_type'], 'idx_applicant_loan_type');
            $table->index('repayment_status', 'idx_repayment_status');
            $table->index('is_to_be_closed', 'idx_loan_closure');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('existing_loans');
    }
};
