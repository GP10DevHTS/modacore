<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Drop old status
            $table->dropColumn('status');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('order_status')->default('draft')->after('supplier_id'); // draft, approved, sent, cancelled
            $table->string('receipt_status')->default('not_received')->after('order_status'); // not_received, partially_received, fully_received
            $table->string('invoice_status')->default('not_invoiced')->after('receipt_status'); // not_invoiced, partially_invoiced, fully_invoiced
            $table->string('payment_status')->default('unpaid')->after('invoice_status'); // unpaid, partially_paid, fully_paid
            $table->string('closure_status')->default('open')->after('payment_status'); // open, closed, force_closed
            $table->string('cancellation_type')->nullable()->after('closure_status');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('status', ['draft', 'sent', 'received', 'cancelled'])->default('draft');
            $table->dropColumn(['order_status', 'receipt_status', 'invoice_status', 'payment_status', 'closure_status', 'cancellation_type']);
        });
    }
};
