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
        Schema::table('inventory_variants', function (Blueprint $table) {
            $table->string('size')->nullable()->change();
            $table->string('color')->nullable()->change();
            $table->string('label')->nullable()->after('color');
            $table->unsignedInteger('stock_quantity')->default(0)->after('label');
            $table->boolean('is_active')->default(true)->after('stock_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_variants', function (Blueprint $table) {
            $table->dropColumn(['label', 'stock_quantity', 'is_active']);
            $table->string('size')->nullable(false)->change();
            $table->string('color')->nullable(false)->change();
        });
    }
};
