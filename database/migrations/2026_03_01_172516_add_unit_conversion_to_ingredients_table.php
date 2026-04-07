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
        Schema::table('ingredients', function (Blueprint $table) {
            $table->string('purchase_unit')->nullable()->after('bulk_quantity'); // Karung, Dus, pack, Liter
            $table->string('bulk_unit_type')->nullable()->after('purchase_unit'); // kg, gram, liter, ml, pcs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn(['purchase_unit', 'bulk_unit_type']);
        });
    }
};
