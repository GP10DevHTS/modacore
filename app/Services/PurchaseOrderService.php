<?php

namespace App\Services;

use App\Enums\ClosureStatus;
use App\Enums\OrderStatus;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\InventoryItem;
use App\Models\PoAuditLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceItem;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    // ── Order lifecycle transitions ──────────────────────────────────────────

    public function approve(PurchaseOrder $order): void
    {
        abort_if($order->order_status !== OrderStatus::Draft, 422, 'Only draft orders can be approved.');

        DB::transaction(function () use ($order) {
            $order->update([
                'order_status' => OrderStatus::Approved,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);
            $this->log($order, 'approved', 'Order approved.');
        });
    }

    public function send(PurchaseOrder $order): void
    {
        abort_if(
            ! in_array($order->order_status, [OrderStatus::Draft, OrderStatus::Approved]),
            422,
            'Only draft or approved orders can be sent.'
        );

        DB::transaction(function () use ($order) {
            $order->update([
                'order_status' => OrderStatus::Sent,
                'sent_at' => now(),
            ]);
            $this->log($order, 'sent', 'Order sent to supplier.');
        });
    }

    // ── Goods Receipt (GRN) ──────────────────────────────────────────────────

    /**
     * @param  array<int, array{purchase_order_item_id: int, inventory_item_id: int, quantity_received: int, notes: ?string}>  $lines
     */
    public function receiveGoods(PurchaseOrder $order, string $receivedDate, array $lines, ?string $notes = null): GoodsReceipt
    {
        abort_if($order->order_status !== OrderStatus::Sent, 422, 'Order must be sent before goods can be received.');
        abort_if(empty($lines), 422, 'At least one line item is required.');

        return DB::transaction(function () use ($order, $receivedDate, $lines, $notes) {
            $grn = GoodsReceipt::create([
                'grn_number' => GoodsReceipt::generateGrnNumber(),
                'purchase_order_id' => $order->id,
                'received_date' => $receivedDate,
                'notes' => $notes,
                'received_by' => auth()->id(),
            ]);

            foreach ($lines as $line) {
                if (($line['quantity_received'] ?? 0) <= 0) {
                    continue;
                }

                $purchaseOrderItem = PurchaseOrderItem::with('inventoryItem')->findOrFail($line['purchase_order_item_id']);

                GoodsReceiptItem::create([
                    'goods_receipt_id' => $grn->id,
                    'purchase_order_item_id' => $line['purchase_order_item_id'],
                    'inventory_item_id' => $line['inventory_item_id'],
                    'quantity_received' => $line['quantity_received'],
                    'notes' => $line['notes'] ?? null,
                ]);

                // Increment received_quantity on the PO item
                $order->items()
                    ->where('id', $line['purchase_order_item_id'])
                    ->increment('received_quantity', $line['quantity_received']);

                // Update physical stock
                InventoryItem::where('id', $line['inventory_item_id'])
                    ->increment('stock_quantity', $line['quantity_received']);

                InventoryItem::where('id', $line['inventory_item_id'])
                    ->increment('available_quantity', $line['quantity_received']);

                if (! empty($purchaseOrderItem->variant_value_ids)) {
                    $skuService = app(InventorySkuService::class);

                    for ($received = 0; $received < $line['quantity_received']; $received++) {
                        $skuService->createTrackedVariant(
                            $purchaseOrderItem->inventoryItem,
                            $purchaseOrderItem->variant_value_ids,
                            null,
                            (float) $purchaseOrderItem->unit_cost,
                        );
                    }
                }
            }

            $order->recomputeReceiptStatus();
            $this->log($order, 'goods_received', "GRN {$grn->grn_number} recorded.");

            return $grn;
        });
    }

    // ── Supplier Invoice ─────────────────────────────────────────────────────

    /**
     * @param  array<int, array{purchase_order_item_id: int, inventory_item_id: int, quantity: int, unit_cost: float}>  $lines
     */
    public function createInvoice(
        PurchaseOrder $order,
        string $invoiceDate,
        array $lines,
        ?string $supplierRef = null,
        ?string $dueDate = null,
        ?string $notes = null
    ): SupplierInvoice {
        abort_if($order->order_status === OrderStatus::Cancelled, 422, 'Cannot invoice a cancelled order.');

        return DB::transaction(function () use ($order, $invoiceDate, $lines, $supplierRef, $dueDate, $notes) {
            $total = collect($lines)->sum(fn ($l) => $l['quantity'] * $l['unit_cost']);

            $invoice = SupplierInvoice::create([
                'invoice_number' => SupplierInvoice::generateInvoiceNumber(),
                'purchase_order_id' => $order->id,
                'supplier_invoice_ref' => $supplierRef,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'total_amount' => $total,
                'notes' => $notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($lines as $line) {
                if (($line['quantity'] ?? 0) <= 0) {
                    continue;
                }

                SupplierInvoiceItem::create([
                    'supplier_invoice_id' => $invoice->id,
                    'purchase_order_item_id' => $line['purchase_order_item_id'],
                    'inventory_item_id' => $line['inventory_item_id'],
                    'quantity' => $line['quantity'],
                    'unit_cost' => $line['unit_cost'],
                    'subtotal' => round($line['quantity'] * $line['unit_cost'], 2),
                ]);

                $order->items()
                    ->where('id', $line['purchase_order_item_id'])
                    ->increment('invoiced_quantity', $line['quantity']);
            }

            $order->recomputeInvoiceStatus();
            $this->log($order, 'invoice_created', "Invoice {$invoice->invoice_number} recorded.");

            return $invoice;
        });
    }

    // ── Payment ──────────────────────────────────────────────────────────────

    public function recordPayment(
        SupplierInvoice $invoice,
        float $amount,
        string $paymentDate,
        string $method = 'bank_transfer',
        ?string $externalRef = null,
        ?string $notes = null
    ): SupplierPayment {
        abort_if($amount <= 0, 422, 'Payment amount must be positive.');

        $order = $invoice->purchaseOrder;

        return DB::transaction(function () use ($invoice, $order, $amount, $paymentDate, $method, $externalRef, $notes) {
            $payment = SupplierPayment::create([
                'payment_reference' => SupplierPayment::generatePaymentReference(),
                'supplier_invoice_id' => $invoice->id,
                'purchase_order_id' => $order->id,
                'amount' => $amount,
                'payment_date' => $paymentDate,
                'payment_method' => $method,
                'external_reference' => $externalRef,
                'notes' => $notes,
                'recorded_by' => auth()->id(),
            ]);

            $invoice->recomputePaymentStatus();
            $this->log($order, 'payment_recorded', "Payment {$payment->payment_reference} of {$amount} recorded.");

            // Auto-close PO when fully paid and fully received
            if (
                $order->fresh()->payment_status->value === 'fully_paid'
                && $order->receipt_status->value === 'fully_received'
            ) {
                $order->update(['closure_status' => ClosureStatus::Closed]);
                $this->log($order, 'closed', 'Order automatically closed after full payment and receipt.');
            }

            return $payment;
        });
    }

    // ── Audit helper ─────────────────────────────────────────────────────────

    public function log(PurchaseOrder $order, string $action, string $description, array $old = [], array $new = []): void
    {
        PoAuditLog::create([
            'purchase_order_id' => $order->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'old_values' => $old ?: null,
            'new_values' => $new ?: null,
        ]);
    }
}
