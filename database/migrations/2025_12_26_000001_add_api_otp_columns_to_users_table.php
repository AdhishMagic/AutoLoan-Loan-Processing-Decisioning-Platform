<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('api_otp_code_hash')->nullable()->after('remember_token');
            $table->timestamp('api_otp_expires_at')->nullable()->after('api_otp_code_hash');
            $table->timestamp('api_otp_verified_at')->nullable()->after('api_otp_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['api_otp_code_hash', 'api_otp_expires_at', 'api_otp_verified_at']);
        });
    }
};
