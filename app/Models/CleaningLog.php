<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CleaningLog extends Model
{
    protected $fillable = [
        'booking_item_id',
        'inventory_item_id',
        'inventory_variant_id',
        'booking_id',
        'quantity',
        'sent_to_cleaning_at',
        'returned_from_cleaning_at',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'sent_to_cleaning_at' => 'datetime',
            'returned_from_cleaning_at' => 'datetime',
            'quantity' => 'integer',
        ];
    }

    public function bookingItem(): BelongsTo
    {
        return $this->belongsTo(BookingItem::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(InventoryVariant::class, 'inventory_variant_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
