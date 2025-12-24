<?php

/**
 * Migration: Extend the `loan_decisions` table to store automated underwriting outputs.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_decisions', function (Blueprint $table) {
            // How this decision was produced.
            $table->string('source', 20)->default('MANUAL')->after('decided_by');

            // Underwriting engine metadata.
            $table->string('engine_name', 50)->nullable()->after('source');
            $table->string('engine_version', 20)->nullable()->after('engine_name');

            // Rule set used to produce this decision.
            $table->unsignedBigInteger('underwriting_rule_id')->nullable()->after('engine_version');
            $table->string('underwriting_rule_name', 100)->nullable()->after('underwriting_rule_id');

            // Immutable snapshots for auditability.
            $table->jsonb('underwriting_rule_snapshot')->nullable()->after('underwriting_rule_name');
            $table->jsonb('facts_snapshot')->nullable()->after('underwriting_rule_snapshot');

            // Final computed result.
            $table->integer('score')->nullable()->after('facts_snapshot');
            $table->string('decision_status', 30)->nullable()->after('score');
            $table->jsonb('reasons')->nullable()->after('decision_status');
            $table->jsonb('trace')->nullable()->after('reasons');
            $table->timestamp('executed_at')->nullable()->after('trace');

            $table->index(['loan_application_id', 'created_at']);

            $table->foreign('underwriting_rule_id')
                ->references('id')
                ->on('underwriting_rules')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('loan_decisions', function (Blueprint $table) {
            $table->dropForeign(['underwriting_rule_id']);
            $table->dropIndex(['loan_application_id', 'created_at']);

            $table->dropColumn([
                'source',
                'engine_name',
                'engine_version',
                'underwriting_rule_id',
                'underwriting_rule_name',
                'underwriting_rule_snapshot',
                'facts_snapshot',
                'score',
                'decision_status',
                'reasons',
                'trace',
                'executed_at',
            ]);
        });
    }
};
