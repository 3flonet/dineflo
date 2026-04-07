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
        Schema::create('app_features', function (Blueprint $table) {
            $table->id();
            $table->string('tab');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('badge')->default('Standar');
            $table->text('short_description');
            $table->longText('long_description')->nullable();
            $table->string('image_url')->nullable();
            $table->json('bullets')->nullable();
            $table->integer('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_features');
    }
};
