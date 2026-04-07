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
            $table->string('email_marketing_provider')->default('system'); // system, custom
            $table->string('email_marketing_smtp_host')->nullable();
            $table->integer('email_marketing_smtp_port')->nullable();
            $table->string('email_marketing_smtp_username')->nullable();
            $table->text('email_marketing_smtp_password')->nullable();
            $table->string('email_marketing_smtp_encryption')->nullable();
            $table->string('email_marketing_smtp_from_address')->nullable();
            $table->string('email_marketing_smtp_from_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'email_marketing_provider',
                'email_marketing_smtp_host',
                'email_marketing_smtp_port',
                'email_marketing_smtp_username',
                'email_marketing_smtp_password',
                'email_marketing_smtp_encryption',
                'email_marketing_smtp_from_address',
                'email_marketing_smtp_from_name',
            ]);
        });
    }
};
