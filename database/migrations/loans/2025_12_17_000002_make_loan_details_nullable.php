<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->string('loan_type', 50)->nullable()->change();
            $table->decimal('requested_amount', 12, 2)->nullable()->change();
            $table->integer('tenure_months')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Reverting usually requires knowing the original state accurately.
        // We assume they were not null.
        // However, converting nulls back to not-null might fail if data exists.
        // We will define the down method but it might be risky in production without data cleanup.
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->string('loan_type', 50)->nullable(false)->change();
            $table->decimal('requested_amount', 12, 2)->nullable(false)->change();
            $table->integer('tenure_months')->nullable(false)->change();
        });
    }
};
