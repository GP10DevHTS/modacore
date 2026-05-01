<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->decimal('cost_price', 12, 2)->nullable()->after('base_rental_price');
            $table->unsignedInteger('available_quantity')->default(0)->after('stock_quantity');
        });

        // Seed available_quantity from stock_quantity for existing records
        DB::table('inventory_items')->update(['available_quantity' => DB::raw('stock_quantity')]);

        Schema::table('inventory_variants', function (Blueprint $table) {
            $table->decimal('cost_price', 12, 2)->nullable()->after('rental_price');
            $table->unsignedInteger('available_quantity')->default(0)->after('stock_quantity');
        });

        DB::table('inventory_variants')->update(['available_quantity' => DB::raw('stock_quantity')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'available_quantity']);
        });

        Schema::table('inventory_variants', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'available_quantity']);
        });
    }
};
