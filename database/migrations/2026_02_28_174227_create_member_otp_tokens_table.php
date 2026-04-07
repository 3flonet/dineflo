<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_otp_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('token', 6);           // 6-digit OTP
            $table->string('token_hash');          // bcrypt hash untuk keamanan
            $table->timestamp('expires_at');       // 5 menit
            $table->boolean('is_used')->default(false);
            $table->timestamps();

            $table->index(['member_id', 'is_used']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_otp_tokens');
    }
};
