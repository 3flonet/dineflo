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
        Schema::table('menu_items', function (Blueprint $table) {
            if (!Schema::hasColumn('menu_items', 'allergens')) {
                $table->json('allergens')->nullable()->after('prep_time');
            }
            if (!Schema::hasColumn('menu_items', 'manage_stock')) {
                $table->boolean('manage_stock')->default(false)->after('allergens');
            }
            if (!Schema::hasColumn('menu_items', 'stock_quantity')) {
                $table->integer('stock_quantity')->default(0)->after('manage_stock');
            }
            if (!Schema::hasColumn('menu_items', 'low_stock_threshold')) {
                $table->integer('low_stock_threshold')->default(5)->after('stock_quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $columns = ['allergens', 'manage_stock', 'stock_quantity', 'low_stock_threshold'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('menu_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
