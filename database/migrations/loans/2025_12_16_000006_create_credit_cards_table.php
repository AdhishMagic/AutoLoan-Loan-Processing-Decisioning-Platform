<?php

/**
 * Migration: Create the `credit_cards` table.
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
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('applicant_id');
            
            // Card Details
            $table->string('card_issuer', 200); // HDFC, ICICI, SBI, etc.
            $table->string('card_type', 100)->nullable(); // Visa, Mastercard, Rupay, Amex
            $table->enum('card_variant', [
                'BASIC',
                'SILVER',
                'GOLD',
                'PLATINUM',
                'TITANIUM',
                'SIGNATURE',
                'OTHER'
            ])->nullable();
            
            $table->string('card_number_last_4', 4); // Only last 4 digits for security
            $table->string('card_holder_name', 200);
            
            // Credit Limit
            $table->decimal('credit_limit', 12, 2);
            $table->decimal('available_credit', 12, 2)->nullable();
            $table->decimal('utilized_credit', 12, 2)->nullable();
            $table->decimal('credit_utilization_percentage', 5, 2)->nullable();
            
            // Card Status
            $table->enum('card_status', [
                'ACTIVE',
                'INACTIVE',
                'BLOCKED',
                'CLOSED',
                'SURRENDERED'
            ])->default('ACTIVE');
            
            $table->date('card_issue_date')->nullable();
            $table->date('card_expiry_date')->nullable();
            $table->integer('card_vintage_months')->nullable();
            
            // Payment Behavior
            $table->decimal('average_monthly_spend', 12, 2)->nullable();
            $table->decimal('current_outstanding', 12, 2)->nullable();
            $table->boolean('is_payment_regular')->default(true);
            $table->integer('missed_payment_count')->default(0);
            $table->date('last_payment_date')->nullable();
            
            // Verification
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->text('verification_notes')->nullable();
            
            // Documents
            $table->string('card_statement_path', 500)->nullable();
            
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
            $table->index('applicant_id', 'idx_applicant_credit_card');
            $table->index('card_status', 'idx_card_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_cards');
    }
};
