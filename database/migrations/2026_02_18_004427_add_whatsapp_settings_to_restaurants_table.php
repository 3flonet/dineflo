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
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('wa_provider')->nullable()->after('is_active'); // fonnte, wablas, etc
            $table->string('wa_api_key')->nullable()->after('wa_provider');
            $table->string('wa_number')->nullable()->after('wa_api_key');
            $table->boolean('wa_is_active')->default(false)->after('wa_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['wa_provider', 'wa_api_key', 'wa_number', 'wa_is_active']);
        });
    }
};
