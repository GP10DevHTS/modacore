<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_import_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_import_id')->constrained()->cascadeOnDelete();
            $table->string('importable_type');
            $table->unsignedBigInteger('importable_id');
            $table->timestamps();

            $table->index(['importable_type', 'importable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_import_items');
    }
};
