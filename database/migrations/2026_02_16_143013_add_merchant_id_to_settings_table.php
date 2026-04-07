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
        // Spatie Settings uses a specific table structure (group, name, payload)
        // We just need to ensure the row exists so the class can load it.
        \DB::table('settings')->insertOrIgnore([
            'group' => 'general',
            'name' => 'midtrans_merchant_id',
            'payload' => json_encode(''),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
};
