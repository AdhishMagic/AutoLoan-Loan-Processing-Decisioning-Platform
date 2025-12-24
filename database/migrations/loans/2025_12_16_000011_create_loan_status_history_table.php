<?php

/**
 * Migration: Create the `loan_status_history` table (status change audit trail).
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
        Schema::create('loan_status_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_application_id');
            
            // Status Information
            $table->string('previous_status', 50)->nullable();
            $table->string('current_status', 50);
            
            // Change Details
            $table->enum('action_type', [
                'STATUS_CHANGE',
                'ASSIGNMENT',
                'SUBMISSION',
                'VERIFICATION',
                'APPROVAL',
                'REJECTION',
                'DISBURSEMENT',
                'CANCELLATION',
                'WITHDRAWAL',
                'DOCUMENT_UPLOAD',
                'COMMENT_ADDED',
                'QUERY_RAISED',
                'QUERY_RESOLVED',
                'ESCALATION',
                'OTHER'
            ]);
            
            $table->string('action_title', 200);
            $table->text('action_description')->nullable();
            $table->text('reason')->nullable();
            
            // Actor Information
            $table->unsignedBigInteger('performed_by');
            $table->enum('actor_role', [
                'APPLICANT',
                'CO_APPLICANT',
                'LOAN_OFFICER',
                'CREDIT_MANAGER',
                'UNDERWRITER',
                'LEGAL_TEAM',
                'TECHNICAL_TEAM',
                'APPROVER',
                'SYSTEM',
                'ADMIN'
            ])->nullable();
            
            // Stage Information
            $table->string('stage', 100)->nullable(); // KYC, Credit Check, Technical, Legal, etc.
            $table->integer('stage_order')->nullable();
            
            // Assignment
            $table->unsignedBigInteger('assigned_from')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            
            // Time Tracking
            $table->timestamp('action_timestamp')->useCurrent();
            $table->integer('time_taken_minutes')->nullable(); // Time taken at this stage
            
            // Metadata
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->text('additional_data')->nullable(); // JSON for any extra information
            
            // Notification Status
            $table->boolean('notification_sent')->default(false);
            $table->timestamp('notification_sent_at')->nullable();
            
            // Documents attached with this action
            $table->text('attached_documents')->nullable(); // JSON array of document IDs
            
            // Priority/Urgency
            $table->enum('priority', [
                'LOW',
                'NORMAL',
                'HIGH',
                'URGENT',
                'CRITICAL'
            ])->default('NORMAL');
            
            // SLA Tracking
            $table->timestamp('sla_deadline')->nullable();
            $table->boolean('is_sla_breached')->default(false);
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('loan_application_id')
                  ->references('id')
                  ->on('loan_applications')
                  ->onDelete('cascade');
            
            $table->foreign('performed_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->foreign('assigned_from')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            $table->foreign('assigned_to')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            // Indexes
            $table->index('loan_application_id', 'idx_loan_history');
            $table->index(['loan_application_id', 'action_timestamp'], 'idx_loan_timeline');
            $table->index(['current_status', 'action_type'], 'idx_status_action');
            $table->index('performed_by', 'idx_performer');
            $table->index('action_timestamp', 'idx_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_status_history');
    }
};
