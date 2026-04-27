<?php

namespace App\Models;

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
        'po_number', 'supplier_id', 'total_amount',
        'notes', 'expected_at', 'received_at', 'created_by',
        'order_status', 'receipt_status', 'invoice_status',
        'payment_status', 'closure_status', 'cancellation_type'
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'expected_at' => 'date',
            'received_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(PoApproval::class);
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function supplierInvoices(): HasMany
    {
        return $this->hasMany(SupplierInvoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ReturnToSupplier::class);
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function auditTrails(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(AuditTrail::class, 'auditable');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generatePoNumber(): string
    {
        $last = static::withTrashed()->latest('id')->first();
        $next = $last ? ($last->id + 1) : 1;

        return 'PO-'.str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
