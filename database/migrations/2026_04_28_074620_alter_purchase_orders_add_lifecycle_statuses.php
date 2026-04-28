<?php

use App\Enums\ClosureStatus;
use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReceiptStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['status', 'received_at']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('order_status')->default(OrderStatus::Draft->value)->after('supplier_id');
            $table->string('receipt_status')->default(ReceiptStatus::NotReceived->value)->after('order_status');
            $table->string('invoice_status')->default(InvoiceStatus::NotInvoiced->value)->after('receipt_status');
            $table->string('payment_status')->default(PaymentStatus::Unpaid->value)->after('invoice_status');
            $table->string('closure_status')->default(ClosureStatus::Open->value)->after('payment_status');

            $table->timestamp('approved_at')->nullable()->after('notes');
            $table->timestamp('sent_at')->nullable()->after('approved_at');
            $table->timestamp('cancelled_at')->nullable()->after('sent_at');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('cancelled_at');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn([
                'order_status', 'receipt_status', 'invoice_status', 'payment_status', 'closure_status',
                'approved_at', 'sent_at', 'cancelled_at', 'approved_by', 'cancelled_by',
            ]);
            $table->enum('status', ['draft', 'sent', 'received', 'cancelled'])->default('draft');
            $table->timestamp('received_at')->nullable();
        });
    }
};
