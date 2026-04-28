<?php

use App\Enums\CancellationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->string('cancellation_type')->default(CancellationType::Direct->value);
            $table->text('reason');
            $table->boolean('requires_rts')->default(false);
            $table->boolean('requires_credit_note')->default(false);
            $table->boolean('requires_refund')->default(false);
            $table->boolean('rts_completed')->default(false);
            $table->boolean('credit_note_completed')->default(false);
            $table->boolean('refund_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_cancellations');
    }
};
