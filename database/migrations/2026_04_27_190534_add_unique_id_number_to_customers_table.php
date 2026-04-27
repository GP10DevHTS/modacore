<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nullify duplicate id_numbers so the unique constraint can be applied cleanly.
        DB::statement("
            UPDATE customers SET id_number = NULL
            WHERE id NOT IN (
                SELECT MIN(id) FROM customers
                WHERE id_number IS NOT NULL
                GROUP BY id_number
            )
            AND id_number IS NOT NULL
        ");

        Schema::table('customers', function (Blueprint $table) {
            $table->string('id_number')->nullable()->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['id_number']);
        });
    }
};
