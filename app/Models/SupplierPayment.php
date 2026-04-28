<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPayment extends Model
{
    protected $fillable = [
        'payment_reference', 'supplier_invoice_id', 'purchase_order_id',
        'amount', 'payment_date', 'payment_method', 'external_reference', 'notes', 'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function supplierInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function generatePaymentReference(): string
    {
        $last = static::latest('id')->first();
        $next = $last ? ($last->id + 1) : 1;

        return 'PAY-'.str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
