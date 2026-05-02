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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->json('variant_value_ids')->nullable()->after('inventory_variant_id');
            $table->string('variant_composition_key')->nullable()->after('variant_value_ids')->index();
            $table->string('variant_composition_label')->nullable()->after('variant_composition_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['variant_value_ids', 'variant_composition_key', 'variant_composition_label']);
        });
    }
};
