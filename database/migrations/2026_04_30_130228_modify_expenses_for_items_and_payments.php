<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Clear demo-seeded financial records before restructuring
        DB::table('expenses')->delete();

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_status_index');
            $table->dropColumn(['payment_method', 'status']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('item_id')
                ->nullable()
                ->after('expense_number')
                ->constrained('expense_items')
                ->nullOnDelete();

            $table->string('payment_status', 20)
                ->default('unpaid')
                ->after('notes')
                ->index();
        });
    }

    public function down(): void
    {
        DB::table('expense_payments')->delete();
        DB::table('expenses')->delete();

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropIndex('expenses_payment_status_index');
            $table->dropColumn(['item_id', 'payment_status']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->enum('payment_method', ['cash', 'card', 'mobile_money'])->default('cash');
            $table->enum('status', ['draft', 'approved'])->default('draft')->index();
        });
    }
};
