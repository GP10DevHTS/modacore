<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierInvoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number', 'purchase_order_id', 'supplier_invoice_ref',
        'invoice_date', 'due_date', 'total_amount', 'payment_status', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'total_amount' => 'decimal:2',
            'payment_status' => PaymentStatus::class,
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SupplierInvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function totalPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function outstandingBalance(): float
    {
        return max(0, (float) $this->total_amount - $this->totalPaid());
    }

    public function recomputePaymentStatus(): void
    {
        $paid = $this->totalPaid();

        $status = match (true) {
            $paid <= 0 => PaymentStatus::Unpaid,
            $paid >= (float) $this->total_amount => PaymentStatus::FullyPaid,
            default => PaymentStatus::PartiallyPaid,
        };

        $this->update(['payment_status' => $status]);
        $this->purchaseOrder->recomputePaymentStatus();
    }

    public static function generateInvoiceNumber(): string
    {
        $last = static::withTrashed()->latest('id')->first();
        $next = $last ? ($last->id + 1) : 1;

        return 'INV-'.str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
