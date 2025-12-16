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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('applicant_id');
            
            // Account Type
            $table->enum('account_type', [
                'SAVINGS',
                'CURRENT',
                'SALARY',
                'OVERDRAFT',
                'CASH_CREDIT',
                'NRE',
                'NRO'
            ]);
            
            // Bank Details
            $table->string('bank_name', 200);
            $table->string('branch_name', 200)->nullable();
            $table->string('ifsc_code', 11)->index();
            $table->string('micr_code', 9)->nullable();
            $table->string('swift_code', 11)->nullable();
            
            // Account Details
            $table->string('account_number', 30);
            $table->string('account_holder_name', 200);
            $table->date('account_opening_date')->nullable();
            $table->integer('account_vintage_months')->nullable();
            
            // Account Status
            $table->enum('account_status', [
                'ACTIVE',
                'INACTIVE',
                'DORMANT',
                'CLOSED'
            ])->default('ACTIVE');
            
            // Balances
            $table->decimal('average_monthly_balance', 12, 2)->nullable();
            $table->decimal('current_balance', 12, 2)->nullable();
            $table->decimal('minimum_balance', 12, 2)->nullable();
            
            // Transaction Activity (last 6 months)
            $table->integer('monthly_credit_count')->nullable();
            $table->decimal('monthly_credit_amount', 14, 2)->nullable();
            $table->integer('monthly_debit_count')->nullable();
            $table->decimal('monthly_debit_amount', 14, 2)->nullable();
            
            // Banking Behavior
            $table->integer('bounced_cheque_count')->default(0);
            $table->integer('returned_emi_count')->default(0);
            $table->boolean('has_overdraft_facility')->default(false);
            $table->decimal('overdraft_limit', 12, 2)->nullable();
            
            // Purpose Flags
            $table->boolean('is_salary_account')->default(false);
            $table->boolean('is_primary_account')->default(false);
            $table->boolean('is_loan_disbursement_account')->default(false);
            
            // Verification
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->enum('verification_method', [
                'PENNY_DROP',
                'BANK_STATEMENT',
                'CANCELLED_CHEQUE',
                'PASSBOOK',
                'NOT_VERIFIED'
            ])->default('NOT_VERIFIED');
            $table->text('verification_notes')->nullable();
            
            // Documents
            $table->string('bank_statement_path', 500)->nullable();
            $table->string('cancelled_cheque_path', 500)->nullable();
            $table->string('passbook_front_page_path', 500)->nullable();
            
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
            $table->index(['applicant_id', 'account_type'], 'idx_applicant_account_type');
            $table->index(['account_number', 'ifsc_code'], 'idx_account_ifsc');
            $table->index('is_primary_account', 'idx_primary_account');
        });

        // Add foreign key to income_details
        Schema::table('income_details', function (Blueprint $table) {
            $table->foreign('salary_bank_account_id')
                  ->references('id')
                  ->on('bank_accounts')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('income_details', function (Blueprint $table) {
            $table->dropForeign(['salary_bank_account_id']);
        });
        
        Schema::dropIfExists('bank_accounts');
    }
};
