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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Percentage or Fixed amount
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2);
            
            // Scope
            $table->enum('scope', ['all', 'categories', 'items'])->default('all');
            
            // Status and Schedule
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable(); // For special event like Valentines
            $table->date('end_date')->nullable();
            
            // Happy Hour specifics
            $table->time('start_time')->nullable(); 
            $table->time('end_time')->nullable();
            $table->boolean('is_recurring')->default(false); 
            $table->json('days_of_week')->nullable(); // ["Monday", "Tuesday"]
            
            $table->timestamps();
        });

        Schema::create('discount_menu_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')->constrained('discounts')->cascadeOnDelete();
            $table->foreignId('menu_category_id')->constrained('menu_categories')->cascadeOnDelete();
        });

        Schema::create('discount_menu_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')->constrained('discounts')->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained('menu_items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_menu_item');
        Schema::dropIfExists('discount_menu_category');
        Schema::dropIfExists('discounts');
    }
};
