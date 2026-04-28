<?php

namespace App\Models;

use Database\Factories\PurchaseOrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrderItem extends Model
{
    /** @use HasFactory<PurchaseOrderItemFactory> */
    use HasFactory;

    protected $fillable = [
        'purchase_order_id', 'inventory_item_id', 'quantity',
        'received_quantity', 'invoiced_quantity', 'unit_cost', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'unit_cost' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function goodsReceiptItems(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function supplierInvoiceItems(): HasMany
    {
        return $this->hasMany(SupplierInvoiceItem::class);
    }

    public function pendingReceiptQuantity(): int
    {
        return max(0, $this->quantity - $this->received_quantity);
    }

    public function pendingInvoiceQuantity(): int
    {
        return max(0, $this->quantity - $this->invoiced_quantity);
    }
}
