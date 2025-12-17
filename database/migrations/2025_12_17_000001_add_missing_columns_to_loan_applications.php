<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('loan_applications', 'application_number')) {
                $table->string('application_number')->unique()->nullable();
            }
            if (!Schema::hasColumn('loan_applications', 'stage_order')) {
                $table->integer('stage_order')->default(0);
            }
            if (!Schema::hasColumn('loan_applications', 'application_date')) {
                $table->date('application_date')->nullable();
            }
            if (!Schema::hasColumn('loan_applications', 'loan_product_type')) {
                $table->string('loan_product_type')->nullable();
            }
            if (!Schema::hasColumn('loan_applications', 'loan_purpose')) {
                $table->string('loan_purpose')->nullable();
            }
            if (!Schema::hasColumn('loan_applications', 'requested_tenure_months')) {
                $table->integer('requested_tenure_months')->nullable();
            }
            if (!Schema::hasColumn('loan_applications', 'current_stage')) {
                 $table->string('current_stage')->nullable();
            }
            
            // Adjustments for consistency if needed
            // The original table had 'loan_type', 'tenure_months', 'interest_rate'.
            // The code uses specific names. We keep original ones or map them?
            // User code uses 'requested_tenure_months'. I added it above.
        });
    }

    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn([
                'application_number', 
                'stage_order', 
                'application_date',
                'loan_product_type',
                'loan_purpose',
                'requested_tenure_months',
                'current_stage'
            ]);
        });
    }
};
