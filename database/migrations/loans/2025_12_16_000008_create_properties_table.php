<?php

/**
 * Migration: Create the `properties` table.
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
        Schema::create('properties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_application_id');
            
            // Property Type
            $table->enum('property_type', [
                'RESIDENTIAL_FLAT',
                'RESIDENTIAL_VILLA',
                'RESIDENTIAL_BUNGALOW',
                'RESIDENTIAL_PLOT',
                'COMMERCIAL_OFFICE',
                'COMMERCIAL_SHOP',
                'COMMERCIAL_PLOT',
                'INDUSTRIAL',
                'AGRICULTURAL',
                'MIXED_USE',
                'OTHER'
            ]);
            
            $table->enum('property_sub_type', [
                '1BHK',
                '2BHK',
                '3BHK',
                '4BHK',
                'STUDIO',
                'PENTHOUSE',
                'NOT_APPLICABLE'
            ])->nullable();
            
            // Construction Status
            $table->enum('construction_status', [
                'READY_TO_MOVE',
                'UNDER_CONSTRUCTION',
                'NEW_BOOKING',
                'RESALE'
            ]);
            
            $table->integer('property_age_years')->nullable();
            
            // Ownership
            $table->enum('ownership_type', [
                'SELF',
                'JOINT',
                'FAMILY',
                'ANCESTRAL',
                'COMPANY',
                'TRUST',
                'OTHER'
            ]);
            
            $table->string('owner_name', 200)->nullable();
            $table->text('co_owners')->nullable(); // JSON array of co-owners
            
            // Identification
            $table->string('property_id', 100)->nullable(); // Builder ID
            $table->string('survey_number', 100)->nullable();
            $table->string('plot_number', 100)->nullable();
            $table->string('khata_number', 100)->nullable();
            $table->string('deed_number', 100)->nullable();
            $table->string('registration_number', 100)->nullable();
            
            // Area Details
            $table->decimal('carpet_area_sqft', 10, 2)->nullable();
            $table->decimal('built_up_area_sqft', 10, 2)->nullable();
            $table->decimal('super_built_up_area_sqft', 10, 2)->nullable();
            $table->decimal('plot_area_sqft', 10, 2)->nullable();
            
            // Valuation
            $table->decimal('market_value', 14, 2);
            $table->decimal('government_value', 14, 2)->nullable();
            $table->decimal('agreement_value', 14, 2)->nullable();
            $table->decimal('stamp_duty_value', 14, 2)->nullable();
            
            $table->decimal('rate_per_sqft', 10, 2)->nullable();
            $table->date('valuation_date')->nullable();
            $table->string('valuation_report_number', 100)->nullable();
            $table->unsignedBigInteger('valued_by')->nullable();
            
            // Builder/Project Details
            $table->string('builder_name', 200)->nullable();
            $table->string('project_name', 200)->nullable();
            $table->string('wing_tower', 100)->nullable();
            $table->string('floor_number', 20)->nullable();
            $table->string('flat_unit_number', 50)->nullable();
            
            // Parking
            $table->integer('parking_count')->default(0);
            $table->enum('parking_type', [
                'COVERED',
                'OPEN',
                'STILT',
                'BASEMENT',
                'NOT_AVAILABLE'
            ])->nullable();
            
            // Legal Status
            $table->enum('property_approval_status', [
                'APPROVED',
                'SANCTIONED',
                'PENDING_APPROVAL',
                'UNAPPROVED',
                'LITIGATION',
                'CLEAR'
            ])->default('PENDING_APPROVAL');
            
            $table->boolean('has_clear_title')->default(false);
            $table->boolean('has_encumbrance')->default(false);
            $table->boolean('is_mortgaged')->default(false);
            $table->string('mortgaged_to', 200)->nullable();
            
            // Amenities
            $table->text('amenities')->nullable(); // JSON array
            
            // Boundaries
            $table->string('boundary_north', 200)->nullable();
            $table->string('boundary_south', 200)->nullable();
            $table->string('boundary_east', 200)->nullable();
            $table->string('boundary_west', 200)->nullable();
            
            // Financial
            $table->decimal('maintenance_charges', 10, 2)->nullable();
            $table->decimal('property_tax_annual', 10, 2)->nullable();
            $table->decimal('society_charges', 10, 2)->nullable();
            
            // Insurance
            $table->boolean('is_insured')->default(false);
            $table->string('insurance_company', 200)->nullable();
            $table->string('insurance_policy_number', 100)->nullable();
            $table->decimal('insurance_amount', 14, 2)->nullable();
            $table->date('insurance_expiry_date')->nullable();
            
            // Verification
            $table->enum('verification_status', [
                'PENDING',
                'IN_PROGRESS',
                'TECHNICAL_VERIFIED',
                'LEGAL_VERIFIED',
                'FULLY_VERIFIED',
                'REJECTED'
            ])->default('PENDING');
            
            $table->timestamp('technical_verified_at')->nullable();
            $table->unsignedBigInteger('technical_verified_by')->nullable();
            $table->timestamp('legal_verified_at')->nullable();
            $table->unsignedBigInteger('legal_verified_by')->nullable();
            $table->text('verification_notes')->nullable();
            
            // Documents
            $table->string('sale_deed_path', 500)->nullable();
            $table->string('title_deed_path', 500)->nullable();
            $table->string('ec_path', 500)->nullable(); // Encumbrance Certificate
            $table->string('tax_receipt_path', 500)->nullable();
            $table->string('approved_plan_path', 500)->nullable();
            $table->string('noc_path', 500)->nullable();
            $table->string('valuation_report_path', 500)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('loan_application_id')
                  ->references('id')
                  ->on('loan_applications')
                  ->onDelete('cascade');
            
            $table->foreign('valued_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            $table->foreign('technical_verified_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            $table->foreign('legal_verified_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            // Indexes
            $table->index('loan_application_id', 'idx_loan_property');
            $table->index(['property_type', 'construction_status'], 'idx_property_type_status');
            $table->index('verification_status', 'idx_verification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
