<?php

namespace App\Models;

use App\Enums\ClosureStatus;
use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReceiptStatus;
use Database\Factories\PurchaseOrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    /** @use HasFactory<PurchaseOrderFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_number', 'supplier_id', 'total_amount', 'notes', 'expected_at',
        'order_status', 'receipt_status', 'invoice_status', 'payment_status', 'closure_status',
        'approved_at', 'sent_at', 'cancelled_at',
        'approved_by', 'cancelled_by', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'expected_at' => 'date',
            'approved_at' => 'datetime',
            'sent_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'order_status' => OrderStatus::class,
            'receipt_status' => ReceiptStatus::class,
            'invoice_status' => InvoiceStatus::class,
            'payment_status' => PaymentStatus::class,
            'closure_status' => ClosureStatus::class,
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function supplierInvoices(): HasMany
    {
        return $this->hasMany(SupplierInvoice::class);
    }

    public function supplierPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(PoAuditLog::class);
    }

    public function cancellation(): HasMany
    {
        return $this->hasMany(PoCancellation::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    // ── Status recomputation ─────────────────────────────────────────────────

    public function recomputeReceiptStatus(): void
    {
        $items = $this->items()->get(['quantity', 'received_quantity']);
        $totalOrdered = $items->sum('quantity');
        $totalReceived = $items->sum('received_quantity');

        $status = match (true) {
            $totalReceived === 0 => ReceiptStatus::NotReceived,
            $totalReceived >= $totalOrdered => ReceiptStatus::FullyReceived,
            default => ReceiptStatus::PartiallyReceived,
        };

        $this->update(['receipt_status' => $status]);
    }

    public function recomputeInvoiceStatus(): void
    {
        $items = $this->items()->get(['quantity', 'invoiced_quantity']);
        $totalOrdered = $items->sum('quantity');
        $totalInvoiced = $items->sum('invoiced_quantity');

        $status = match (true) {
            $totalInvoiced === 0 => InvoiceStatus::NotInvoiced,
            $totalInvoiced >= $totalOrdered => InvoiceStatus::FullyInvoiced,
            default => InvoiceStatus::PartiallyInvoiced,
        };

        $this->update(['invoice_status' => $status]);
    }

    public function recomputePaymentStatus(): void
    {
        $totalInvoiced = $this->supplierInvoices()->sum('total_amount');
        $totalPaid = $this->supplierPayments()->sum('amount');

        $status = match (true) {
            $totalInvoiced == 0 || $totalPaid == 0 => PaymentStatus::Unpaid,
            $totalPaid >= $totalInvoiced => PaymentStatus::FullyPaid,
            default => PaymentStatus::PartiallyPaid,
        };

        $this->update(['payment_status' => $status]);
    }

    // ── Financial helpers ────────────────────────────────────────────────────

    public function totalInvoiced(): float
    {
        return (float) $this->supplierInvoices()->sum('total_amount');
    }

    public function totalPaid(): float
    {
        return (float) $this->supplierPayments()->sum('amount');
    }

    public function outstandingBalance(): float
    {
        return max(0, $this->totalInvoiced() - $this->totalPaid());
    }

    // ── PO number ────────────────────────────────────────────────────────────

    public static function generatePoNumber(): string
    {
        $last = static::withTrashed()->latest('id')->first();
        $next = $last ? ($last->id + 1) : 1;

        return 'PO-'.str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
