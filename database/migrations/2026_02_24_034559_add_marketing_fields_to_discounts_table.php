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
        Schema::table('discounts', function (Blueprint $table) {
            $table->string('code')->nullable()->after('description');
            $table->enum('target_type', ['all', 'members_only', 'tiers_only'])->default('all')->after('code');
            $table->json('target_tiers')->nullable()->after('target_type');
            $table->decimal('min_order_amount', 15, 2)->default(0)->after('value');
            $table->integer('usage_limit')->nullable()->after('target_tiers');
            $table->integer('usage_per_customer')->default(1)->after('usage_limit');
            $table->integer('total_usage')->default(0)->after('usage_per_customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'target_type',
                'target_tiers',
                'min_order_amount',
                'usage_limit',
                'usage_per_customer',
                'total_usage',
            ]);
        });
    }
};
