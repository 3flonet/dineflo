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
            if (!Schema::hasColumn('menu_items', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('description');
            }
        });
        
        Schema::table('tables', function (Blueprint $table) {
            if (!Schema::hasColumn('tables', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            if (Schema::hasColumn('menu_items', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });

        Schema::table('tables', function (Blueprint $table) {
            if (Schema::hasColumn('tables', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};
