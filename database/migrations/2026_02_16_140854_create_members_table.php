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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('whatsapp')->index();
            $table->string('email')->nullable();
            $table->date('birthday')->nullable();
            $table->integer('points_balance')->default(0);
            $table->decimal('total_spent', 15, 2)->default(0);
            $table->enum('tier', ['bronze', 'silver', 'gold'])->default('bronze');
            $table->timestamps();
            
            // Unik per restoran: Satu nomor HP hanya bisa jadi 1 akun di restoran yang sama
            $table->unique(['restaurant_id', 'whatsapp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
