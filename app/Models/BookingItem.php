<?php

namespace App\Models;

use Database\Factories\BookingItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingItem extends Model
{
    /** @use HasFactory<BookingItemFactory> */
    use HasFactory;

    protected $fillable = [
        'booking_id', 'inventory_item_id', 'inventory_variant_id',
        'quantity', 'unit_price', 'subtotal', 'notes',
        'status', 'checked_out_at', 'returned_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'checked_out_at' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }

    public function isCheckedOut(): bool
    {
        return $this->status === 'checked_out';
    }

    public function isReturned(): bool
    {
        return in_array($this->status, ['in_cleaning', 'returned']);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(InventoryVariant::class, 'inventory_variant_id');
    }
}
