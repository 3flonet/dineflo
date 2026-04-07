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
        Schema::table('cash_drawer_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('cash_drawer_logs', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0)->after('order_id');
            }
            if (!Schema::hasColumn('cash_drawer_logs', 'pos_register_session_id')) {
                $table->foreignId('pos_register_session_id')->nullable()->after('amount')->constrained('pos_register_sessions')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_drawer_logs', function (Blueprint $table) {
            if (Schema::hasColumn('cash_drawer_logs', 'pos_register_session_id')) {
                $table->dropConstrainedForeignId('pos_register_session_id');
            }
            if (Schema::hasColumn('cash_drawer_logs', 'amount')) {
                $table->dropColumn('amount');
            }
        });
    }
};
