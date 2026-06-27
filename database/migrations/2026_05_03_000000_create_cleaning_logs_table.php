<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleaning_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_item_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->timestamp('sent_to_cleaning_at');
            $table->timestamp('returned_from_cleaning_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cleaning_logs');
    }
};
