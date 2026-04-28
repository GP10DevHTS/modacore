<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodsReceipt extends Model
{
    protected $fillable = [
        'grn_number', 'purchase_order_id', 'received_date', 'notes', 'received_by',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public static function generateGrnNumber(): string
    {
        $last = static::latest('id')->first();
        $next = $last ? ($last->id + 1) : 1;

        return 'GRN-'.str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
