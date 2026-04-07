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
        Schema::table('system_broadcasts', function (Blueprint $table) {
            // Pesan khusus WA (plain text), digunakan saat channel = 'whatsapp' atau 'both'
            $table->text('wa_message')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_broadcasts', function (Blueprint $table) {
            $table->dropColumn('wa_message');
        });
    }
};
