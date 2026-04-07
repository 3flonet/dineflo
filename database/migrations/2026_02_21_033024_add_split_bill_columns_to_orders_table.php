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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_split_bill')->default(false)->after('payment_status');
            $table->string('split_type')->nullable()->after('is_split_bill'); // equal, custom, item
            $table->decimal('amount_paid', 12, 2)->default(0)->after('total_amount'); // To track partial payments
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_split_bill', 'split_type', 'amount_paid']);
        });
    }
};
