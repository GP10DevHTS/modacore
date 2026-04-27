<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'checked_out',
                'in_cleaning',
                'returned',
            ])->default('pending')->after('notes');

            $table->timestamp('checked_out_at')->nullable()->after('status');
            $table->timestamp('returned_at')->nullable()->after('checked_out_at');
        });
    }

    public function down(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropColumn(['status', 'checked_out_at', 'returned_at']);
        });
    }
};
