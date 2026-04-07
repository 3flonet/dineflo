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
            $table->boolean('tax_enabled')->default(false)->after('currency');
            $table->decimal('tax_percentage', 5, 2)->default(0)->after('tax_enabled');
            $table->json('additional_fees')->nullable()->after('tax_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['tax_enabled', 'tax_percentage', 'additional_fees']);
        });
    }
};
