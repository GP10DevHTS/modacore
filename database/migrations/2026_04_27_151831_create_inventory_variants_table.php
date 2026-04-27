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
        Schema::create('inventory_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();

            $table->string('size');   // S, M, L, XL
            $table->string('color');  // Red, Blue, etc

            $table->decimal('rental_price', 10, 2)->nullable();
            // optional override from base price

            $table->string('sku')->unique()->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_variants');
    }
};
