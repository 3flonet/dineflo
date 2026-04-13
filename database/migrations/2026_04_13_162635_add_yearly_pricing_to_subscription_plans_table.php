<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->boolean('has_yearly')->default(false)->after('billing_period');
            $table->decimal('yearly_price', 15, 2)->nullable()->after('has_yearly');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['has_yearly', 'yearly_price']);
        });
    }
};
