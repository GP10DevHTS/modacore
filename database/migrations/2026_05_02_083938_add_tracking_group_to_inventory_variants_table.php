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
        Schema::table('inventory_variants', function (Blueprint $table) {
            $table->string('composition_key')->nullable()->after('label')->index();
        });

        DB::table('inventory_variants')
            ->select('id')
            ->orderBy('id')
            ->each(function ($variant) {
                $valueIds = DB::table('inventory_variant_attribute_values')
                    ->where('inventory_variant_id', $variant->id)
                    ->orderBy('variant_type_value_id')
                    ->pluck('variant_type_value_id')
                    ->implode('-');

                if ($valueIds !== '') {
                    DB::table('inventory_variants')
                        ->where('id', $variant->id)
                        ->update(['composition_key' => $valueIds]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_variants', function (Blueprint $table) {
            $table->dropColumn('composition_key');
        });
    }
};
