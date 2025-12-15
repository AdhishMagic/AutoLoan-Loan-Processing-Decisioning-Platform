<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_decisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_application_id');
            $table->string('decision', 30);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('decided_by')->nullable();
            $table->timestamps();

            $table->foreign('loan_application_id')->references('id')->on('loan_applications');
            $table->foreign('decided_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_decisions');
    }
};
