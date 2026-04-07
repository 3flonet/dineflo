<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->decimal('admin_fee_percentage', 5, 2)->default(0)->after('amount');
            $table->decimal('admin_fee_amount', 15, 2)->default(0)->after('admin_fee_percentage');
            $table->decimal('net_transfer_amount', 15, 2)->nullable()->after('admin_fee_amount');
        });
    }

    public function down(): void
    {
        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->dropColumn(['admin_fee_percentage', 'admin_fee_amount', 'net_transfer_amount']);
        });
    }
};
