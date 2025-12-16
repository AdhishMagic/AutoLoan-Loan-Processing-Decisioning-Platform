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
        Schema::create('income_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('applicant_id');
            
            // Income Type
            $table->enum('income_type', [
                'SALARY',
                'BUSINESS_INCOME',
                'PROFESSIONAL_INCOME',
                'RENTAL_INCOME',
                'PENSION',
                'INVESTMENT_INCOME',
                'AGRICULTURAL_INCOME',
                'OTHER'
            ]);
            
            // Frequency
            $table->enum('income_frequency', [
                'MONTHLY',
                'QUARTERLY',
                'ANNUALLY'
            ])->default('MONTHLY');
            
            // Income Amounts
            $table->decimal('gross_income_amount', 12, 2);
            $table->decimal('net_income_amount', 12, 2);
            $table->decimal('deductions_amount', 12, 2)->default(0);
            
            // Annual Breakdown
            $table->decimal('gross_annual_income', 14, 2)->nullable();
            $table->decimal('net_annual_income', 14, 2)->nullable();
            
            // Salary Specific
            $table->decimal('basic_salary', 12, 2)->nullable();
            $table->decimal('hra', 12, 2)->nullable();
            $table->decimal('special_allowance', 12, 2)->nullable();
            $table->decimal('variable_pay', 12, 2)->nullable();
            $table->decimal('bonus', 12, 2)->nullable();
            $table->decimal('commission', 12, 2)->nullable();
            $table->decimal('overtime', 12, 2)->nullable();
            $table->decimal('other_allowances', 12, 2)->nullable();
            
            // Deductions
            $table->decimal('pf_deduction', 12, 2)->nullable();
            $table->decimal('professional_tax', 12, 2)->nullable();
            $table->decimal('tds', 12, 2)->nullable();
            $table->decimal('esi', 12, 2)->nullable();
            $table->decimal('loan_deduction', 12, 2)->nullable();
            $table->decimal('other_deductions', 12, 2)->nullable();
            
            // Business/Professional Income
            $table->decimal('turnover', 14, 2)->nullable();
            $table->decimal('net_profit', 14, 2)->nullable();
            $table->decimal('depreciation', 12, 2)->nullable();
            
            // Banking
            $table->enum('salary_mode', [
                'BANK_TRANSFER',
                'CASH',
                'CHEQUE',
                'NOT_APPLICABLE'
            ])->default('BANK_TRANSFER');
            
            $table->uuid('salary_bank_account_id')->nullable(); // Reference to bank_accounts
            
            // Tax Information
            $table->string('itr_filing_status', 50)->nullable(); // Filed, Not Filed
            $table->string('last_itr_year', 10)->nullable(); // FY 2023-24
            $table->decimal('last_itr_income', 14, 2)->nullable();
            
            // Document References
            $table->string('salary_slip_path', 500)->nullable();
            $table->string('form16_path', 500)->nullable();
            $table->string('itr_path', 500)->nullable();
            $table->string('bank_statement_path', 500)->nullable();
            $table->string('audit_report_path', 500)->nullable();
            
            // Verification
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->text('verification_notes')->nullable();
            
            // Reference Period
            $table->string('reference_month', 7)->nullable(); // 2025-01
            $table->string('reference_year', 4)->nullable(); // 2025
            
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
            $table->index(['applicant_id', 'income_type'], 'idx_applicant_income_type');
            $table->index('is_verified', 'idx_income_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_details');
    }
};
