<?php

namespace App\Services;

use App\Enums\CancellationType;
use App\Enums\ClosureStatus;
use App\Enums\OrderStatus;
use App\Enums\ReceiptStatus;
use App\Models\PoCancellation;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CancellationService
{
    /**
     * Determine the cancellation type and requirements based on the PO's current stage.
     *
     * Rules:
     *  - Draft / Approved → Direct cancellation, no side effects
     *  - Sent, not received → Direct + supplier notification log
     *  - Partially or fully received → Requires Return to Supplier (RTS)
     *  - Has invoices → Requires Credit Note
     *  - Has payments → Requires Refund
     */
    public function determineCancellationType(PurchaseOrder $order): CancellationType
    {
        if ($order->supplierPayments()->exists()) {
            return CancellationType::WithRefund;
        }

        if ($order->supplierInvoices()->exists()) {
            return CancellationType::WithCreditNote;
        }

        if (in_array($order->receipt_status, [ReceiptStatus::PartiallyReceived, ReceiptStatus::FullyReceived])) {
            return CancellationType::WithReturnToSupplier;
        }

        return CancellationType::Direct;
    }

    /**
     * Initiate a cancellation request.
     * For Direct type, the PO is cancelled immediately.
     * For all others a PoCancellation record is created and the PO remains in its
     * current state until side-effects are resolved via completeRequirements().
     */
    public function initiate(PurchaseOrder $order, string $reason): PoCancellation
    {
        if ($order->order_status === OrderStatus::Cancelled) {
            throw ValidationException::withMessages(['order' => 'This order is already cancelled.']);
        }

        $type = $this->determineCancellationType($order);

        return DB::transaction(function () use ($order, $reason, $type) {
            $cancellation = PoCancellation::create([
                'purchase_order_id' => $order->id,
                'cancellation_type' => $type,
                'reason' => $reason,
                'requires_rts' => $type === CancellationType::WithReturnToSupplier,
                'requires_credit_note' => $type === CancellationType::WithCreditNote,
                'requires_refund' => $type === CancellationType::WithRefund,
                'cancelled_by' => auth()->id(),
            ]);

            if ($type === CancellationType::Direct) {
                $this->finalize($order, $cancellation);
            }

            app(PurchaseOrderService::class)->log(
                $order,
                'cancellation_initiated',
                "Cancellation initiated ({$type->label()}): {$reason}"
            );

            return $cancellation;
        });
    }

    /**
     * Mark one or more side-effect requirements as completed on the cancellation.
     * Once all requirements are met the PO is cancelled automatically.
     */
    public function completeRequirements(PoCancellation $cancellation, array $flags): void
    {
        $cancellation->update(array_intersect_key($flags, array_flip([
            'rts_completed', 'credit_note_completed', 'refund_completed',
        ])));

        $cancellation->refresh();

        if ($cancellation->isFullyResolved()) {
            DB::transaction(function () use ($cancellation) {
                $order = $cancellation->purchaseOrder;
                $this->finalize($order, $cancellation);
                app(PurchaseOrderService::class)->log(
                    $order,
                    'cancellation_completed',
                    'All cancellation requirements resolved. Order cancelled.'
                );
            });
        }
    }

    private function finalize(PurchaseOrder $order, PoCancellation $cancellation): void
    {
        $cancellation->update(['completed_at' => now()]);

        $order->update([
            'order_status' => OrderStatus::Cancelled,
            'closure_status' => ClosureStatus::ForceClosed,
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
        ]);
    }
}
