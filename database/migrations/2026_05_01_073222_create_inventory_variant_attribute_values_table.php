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
        Schema::create('inventory_variant_attribute_values', function (Blueprint $table) {
            $table->foreignId('inventory_variant_id')->constrained('inventory_variants')->cascadeOnDelete();
            $table->foreignId('variant_type_value_id')->constrained('variant_type_values')->cascadeOnDelete();
            $table->primary(['inventory_variant_id', 'variant_type_value_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_variant_attribute_values');
    }
};
