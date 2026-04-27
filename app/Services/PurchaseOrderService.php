<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceItem;
use App\Models\SupplierPayment;
use App\Models\StockMovement;
use App\Models\ReturnToSupplier;
use App\Models\CreditNote;
use App\Models\Refund;
use App\Models\PoApproval;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PurchaseOrderService
{
    public function updateStatuses(PurchaseOrder $po): void
    {
        $po->load(['items.receiptItems', 'items.invoiceItems', 'payments', 'supplierInvoices']);

        $totalItems = $po->items->sum('quantity');
        $receivedItems = $po->items->sum(fn($item) => $item->receiptItems->sum('quantity_received'));
        $invoicedItems = $po->items->sum(fn($item) => $item->invoiceItems->sum('quantity'));

        // Receipt Status
        if ($receivedItems == 0) {
            $po->receipt_status = 'not_received';
        } elseif ($receivedItems >= $totalItems) {
            $po->receipt_status = 'fully_received';
        } else {
            $po->receipt_status = 'partially_received';
        }

        // Invoice Status
        if ($invoicedItems == 0) {
            $po->invoice_status = 'not_invoiced';
        } elseif ($invoicedItems >= $totalItems) {
            $po->invoice_status = 'fully_invoiced';
        } else {
            $po->invoice_status = 'partially_invoiced';
        }

        // Payment Status
        $totalInvoicedAmount = $po->supplierInvoices->sum('total_amount');
        $totalPaidAmount = $po->payments->sum('amount');

        if ($totalPaidAmount == 0) {
            $po->payment_status = 'unpaid';
        } elseif ($totalPaidAmount >= $totalInvoicedAmount && $totalInvoicedAmount > 0) {
            $po->payment_status = 'fully_paid';
        } else {
            $po->payment_status = 'partially_paid';
        }

        // Closure Status
        if ($po->order_status !== 'cancelled' &&
            $po->receipt_status === 'fully_received' &&
            $po->invoice_status === 'fully_invoiced' &&
            $po->payment_status === 'fully_paid') {
            $po->closure_status = 'closed';
        }

        $po->save();

        $this->logAudit($po, 'status_updated', null, [
            'receipt_status' => $po->receipt_status,
            'invoice_status' => $po->invoice_status,
            'payment_status' => $po->payment_status,
            'closure_status' => $po->closure_status,
        ]);
    }

    public function logAudit($model, string $action, ?array $oldValues = null, ?array $newValues = null, ?string $notes = null): void
    {
        AuditTrail::create([
            'auditable_id' => $model->id,
            'auditable_type' => get_class($model),
            'user_id' => Auth::id(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'notes' => $notes,
        ]);
    }

    public function approveOrder(PurchaseOrder $po, ?string $notes = null): void
    {
        if ($po->order_status !== 'draft') {
            throw new Exception("Only draft orders can be approved.");
        }

        DB::transaction(function () use ($po, $notes) {
            $po->approvals()->create([
                'user_id' => Auth::id() ?? 1,
                'status' => 'approved',
                'notes' => $notes,
            ]);

            $po->update(['order_status' => 'approved']);
            $this->logAudit($po, 'approved', ['order_status' => 'draft'], ['order_status' => 'approved'], $notes);
        });
    }

    public function recordReceipt(PurchaseOrder $po, array $items, string $grnNumber, ?string $notes = null): GoodsReceipt
    {
        if (in_array($po->order_status, ['draft', 'cancelled']) || in_array($po->closure_status, ['closed', 'force_closed'])) {
            throw new Exception("Cannot record receipt for a cancelled or closed Purchase Order.");
        }

        return DB::transaction(function () use ($po, $items, $grnNumber, $notes) {
            $receipt = $po->goodsReceipts()->create([
                'grn_number' => $grnNumber,
                'received_at' => now(),
                'notes' => $notes,
                'received_by' => Auth::id() ?? 1,
            ]);

            foreach ($items as $itemData) {
                $receiptItem = $receipt->items()->create([
                    'purchase_order_item_id' => $itemData['purchase_order_item_id'],
                    'quantity_received' => $itemData['quantity'],
                ]);

                $poItem = PurchaseOrderItem::find($itemData['purchase_order_item_id']);
                $inventoryItem = $poItem->inventoryItem;
                $inventoryItem->increment('stock_quantity', $itemData['quantity']);

                StockMovement::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'quantity' => $itemData['quantity'],
                    'type' => 'receipt',
                    'reference_id' => $receiptItem->id,
                    'reference_type' => GoodsReceiptItem::class,
                    'user_id' => Auth::id() ?? 1,
                ]);
            }

            $this->updateStatuses($po);
            return $receipt;
        });
    }

    public function recordInvoice(PurchaseOrder $po, array $items, string $invoiceNumber, $invoiceDate, $dueDate, float $taxAmount = 0): SupplierInvoice
    {
        if ($po->order_status === 'cancelled') {
            throw new Exception("Cannot record invoice for a cancelled Purchase Order.");
        }

        return DB::transaction(function () use ($po, $items, $invoiceNumber, $invoiceDate, $dueDate, $taxAmount) {
            $totalAmount = 0;
            foreach ($items as $itemData) {
                $totalAmount += $itemData['quantity'] * $itemData['unit_price'];
            }
            $totalAmount += $taxAmount;

            $invoice = $po->supplierInvoices()->create([
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'status' => 'pending',
            ]);

            foreach ($items as $itemData) {
                $invoice->items()->create([
                    'purchase_order_item_id' => $itemData['purchase_order_item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'subtotal' => $itemData['quantity'] * $itemData['unit_price'],
                ]);
            }

            $this->updateStatuses($po);
            return $invoice;
        });
    }

    public function recordPayment(PurchaseOrder $po, SupplierInvoice $invoice, float $amount, string $method, ?string $reference = null): SupplierPayment
    {
        if ($invoice->status === 'cancelled') {
            throw new Exception("Cannot record payment for a cancelled invoice.");
        }

        return DB::transaction(function () use ($po, $invoice, $amount, $method, $reference) {
            $payment = $po->payments()->create([
                'supplier_invoice_id' => $invoice->id,
                'amount' => $amount,
                'payment_method' => $method,
                'reference' => $reference,
                'paid_at' => now(),
                'created_by' => Auth::id() ?? 1,
            ]);

            $totalPaidOnInvoice = $invoice->payments()->sum('amount');
            if ($totalPaidOnInvoice >= $invoice->total_amount) {
                $invoice->update(['status' => 'paid']);
            } else {
                $invoice->update(['status' => 'partially_paid']);
            }

            $this->updateStatuses($po);
            return $payment;
        });
    }

    public function cancelOrder(PurchaseOrder $po, string $reason): void
    {
        if ($po->order_status === 'cancelled') {
            return; // Idempotency
        }

        DB::transaction(function () use ($po, $reason) {
            // 1. Handle Paid Stage
            if ($po->payment_status !== 'unpaid') {
                foreach ($po->payments as $payment) {
                    $refundAmount = $payment->amount;
                    $po->refunds()->create([
                        'supplier_payment_id' => $payment->id,
                        'amount' => $refundAmount,
                        'reference' => 'CANCEL-' . $po->po_number,
                    ]);
                    $this->logAudit($payment, 'refunded_due_to_cancellation', null, ['amount' => $refundAmount]);
                }
            }

            // 2. Handle Invoiced Stage
            if ($po->invoice_status !== 'not_invoiced') {
                foreach ($po->supplierInvoices as $invoice) {
                    if ($invoice->status === 'cancelled') continue;

                    $po->creditNotes()->create([
                        'supplier_invoice_id' => $invoice->id,
                        'cn_number' => 'CN-' . $invoice->invoice_number,
                        'amount' => $invoice->total_amount,
                        'reason' => 'Order Cancellation: ' . $reason,
                    ]);
                    $invoice->update(['status' => 'cancelled']);
                    $this->logAudit($invoice, 'cancelled_by_po_cancellation');
                }
            }

            // 3. Handle Received Stage
            if ($po->receipt_status !== 'not_received') {
                foreach ($po->goodsReceipts as $receipt) {
                    $po->returns()->create([
                        'goods_receipt_id' => $receipt->id,
                        'rts_number' => 'RTS-' . $receipt->grn_number,
                        'reason' => 'Order Cancellation: ' . $reason,
                    ]);

                    foreach ($receipt->items as $receiptItem) {
                        $inventoryItem = $receiptItem->purchaseOrderItem->inventoryItem;
                        $inventoryItem->decrement('stock_quantity', $receiptItem->quantity_received);

                        StockMovement::create([
                            'inventory_item_id' => $inventoryItem->id,
                            'quantity' => -$receiptItem->quantity_received,
                            'type' => 'return',
                            'reference_id' => $receiptItem->id,
                            'reference_type' => GoodsReceiptItem::class,
                            'user_id' => Auth::id() ?? 1,
                        ]);
                    }
                    $this->logAudit($receipt, 'returned_due_to_cancellation');
                }
            }

            // 4. Handle Sent Stage
            if ($po->order_status === 'sent' && $po->receipt_status === 'not_received') {
                $this->logAudit($po, 'supplier_notified_of_cancellation', null, null, 'Notification logged for sent PO.');
            }

            $oldStatus = $po->order_status;
            $po->order_status = 'cancelled';
            $po->closure_status = 'open'; // Re-open if it was force closed? Or keep it? ERP usually means cancelled is terminal.
            $po->cancellation_type = $this->determineCancellationType($po);
            $po->save();

            $this->logAudit($po, 'cancelled', ['order_status' => $oldStatus], ['order_status' => 'cancelled'], $reason);
        });
    }

    protected function determineCancellationType(PurchaseOrder $po): string
    {
        if ($po->payment_status !== 'unpaid') return 'after_payment';
        if ($po->invoice_status !== 'not_invoiced') return 'after_invoicing';
        if ($po->receipt_status !== 'not_received') return 'after_receipt';
        if ($po->order_status === 'sent') return 'after_sent';
        return 'pre_execution';
    }

    public function performThreeWayMatch(PurchaseOrder $po, SupplierInvoice $invoice): array
    {
        $mismatches = [];
        $tolerance = 0.05; // 5% tolerance

        foreach ($invoice->items as $invoiceItem) {
            $poItem = $po->items->where('id', $invoiceItem->purchase_order_item_id)->first();

            if (!$poItem) {
                $mismatches[] = "Invoice item not found in PO.";
                continue;
            }

            // Check PO Price vs Invoice Price
            $priceDiff = abs($invoiceItem->unit_price - $poItem->unit_cost) / $poItem->unit_cost;
            if ($priceDiff > $tolerance) {
                $mismatches[] = "Price mismatch for item {$poItem->id}: PO {$poItem->unit_cost}, Invoice {$invoiceItem->unit_price}";
            }

            // Check Received Quantity vs Invoiced Quantity
            $receivedQty = $poItem->receiptItems->sum('quantity_received');
            if ($invoiceItem->quantity > $receivedQty) {
                $mismatches[] = "Invoiced quantity ({$invoiceItem->quantity}) exceeds received quantity ({$receivedQty}) for item {$poItem->id}.";
            }
        }

        return [
            'matched' => empty($mismatches),
            'mismatches' => $mismatches,
        ];
    }
}
