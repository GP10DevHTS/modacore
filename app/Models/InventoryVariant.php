<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryVariant extends Model
{
    protected $fillable = ['inventory_item_id', 'size', 'color', 'rental_price', 'sku'];

    public function getNameAttribute(): string
    {
        $parts = array_filter([$this->size, $this->color]);

        return implode(' / ', $parts) ?: "Variant #{$this->id}";
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
