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
        Schema::create('inventory_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garment_id')->constrained('garments')->onDelete('cascade');
            $table->string('product_name');
            $table->string('product_size')->nullable();
            $table->integer('quantity');
            $table->string('order_number');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_allocations');
    }
};
