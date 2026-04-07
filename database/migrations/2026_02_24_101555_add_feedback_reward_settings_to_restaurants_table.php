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
            $table->boolean('feedback_reward_enabled')->default(false)->after('loyalty_gold_threshold');
            $table->string('feedback_reward_type')->default('points')->comment('points, voucher');
            $table->integer('feedback_reward_points')->default(0);
            $table->foreignId('feedback_reward_discount_id')->nullable()->constrained('discounts')->nullOnDelete();
            $table->string('feedback_notification_channel')->default('whatsapp')->comment('whatsapp, email, both');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropForeign(['feedback_reward_discount_id']);
            $table->dropColumn([
                'feedback_reward_enabled',
                'feedback_reward_type',
                'feedback_reward_points',
                'feedback_reward_discount_id',
                'feedback_notification_channel'
            ]);
        });
    }
};
