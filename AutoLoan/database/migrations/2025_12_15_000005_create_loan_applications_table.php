<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('loan_type', 50);
            $table->decimal('requested_amount', 12, 2);
            $table->integer('tenure_months');
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->string('status', 30)->default('draft');
            $table->unsignedBigInteger('assigned_officer_id')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('assigned_officer_id')->references('id')->on('users');
            $table->index('status', 'idx_loan_status');
            $table->index('user_id', 'idx_loan_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};
