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
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Polymorphic relationship - can belong to applicant, property, or company
            $table->uuidMorphs('addressable');
            
            // Address Type
            $table->enum('address_type', [
                'CURRENT',
                'PERMANENT',
                'OFFICE',
                'PROPERTY',
                'REGISTERED_OFFICE',
                'OTHER'
            ]);
            
            // Address Details
            $table->text('address_line_1');
            $table->text('address_line_2')->nullable();
            $table->text('address_line_3')->nullable();
            $table->string('landmark', 200)->nullable();
            $table->string('locality', 200)->nullable();
            $table->string('city', 100);
            $table->string('district', 100)->nullable();
            $table->string('state', 100);
            $table->string('country', 100)->default('INDIA');
            $table->string('pincode', 10)->index();
            
            // Verification
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->text('verification_notes')->nullable();
            
            // Proof of Address
            $table->string('proof_type', 50)->nullable(); // ELECTRICITY_BILL, RENT_AGREEMENT, etc.
            $table->string('proof_document_path', 500)->nullable();
            
            // Geo-coordinates (optional for property location)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            
            // Duration (for residential addresses)
            $table->integer('years_at_address')->nullable();
            $table->integer('months_at_address')->nullable();
            
            // Meta
            $table->boolean('is_primary')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('verified_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            // Indexes
            $table->index(['addressable_type', 'addressable_id', 'address_type'], 'idx_addressable_type');
            $table->index(['city', 'state', 'pincode'], 'idx_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
