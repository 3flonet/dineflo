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
        Schema::table('menu_item_ingredients', function (Blueprint $table) {
            $table->decimal('bulk_total_ingredients', 10, 2)->nullable()->after('ingredient_id');
            $table->decimal('bulk_portions', 10, 2)->nullable()->after('bulk_total_ingredients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_item_ingredients', function (Blueprint $table) {
            $table->dropColumn(['bulk_total_ingredients', 'bulk_portions']);
        });
    }
};
