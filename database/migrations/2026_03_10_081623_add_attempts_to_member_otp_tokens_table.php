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
        Schema::table('member_otp_tokens', function (Blueprint $table) {
            $table->string('token')->nullable()->change(); // Secure: allow null for plain token
            $table->integer('attempts')->default(0)->after('token_hash');
        });
    }

    public function down(): void
    {
        Schema::table('member_otp_tokens', function (Blueprint $table) {
            $table->string('token')->nullable(false)->change();
            $table->dropColumn('attempts');
        });
    }
};
