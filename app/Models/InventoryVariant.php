<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryVariant extends Model
{
    protected $fillable = [
        'inventory_item_id', 'size', 'color', 'label',
        'rental_price', 'cost_price', 'sku',
        'stock_quantity', 'available_quantity', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rental_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'available_quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getNameAttribute(): string
    {
        if ($this->relationLoaded('attributeValues') && $this->attributeValues->isNotEmpty()) {
            return $this->attributeValues->map(fn ($v) => $v->label)->join(' / ');
        }

        if ($this->label) {
            return $this->label;
        }

        $parts = array_filter([$this->size, $this->color]);

        return implode(' / ', $parts) ?: "Variant #{$this->id}";
    }

    public function effectiveRentalPrice(): float
    {
        return $this->rental_price !== null
            ? (float) $this->rental_price
            : (float) $this->item->base_rental_price;
    }

    public function effectiveCostPrice(): float
    {
        return $this->cost_price !== null
            ? (float) $this->cost_price
            : (float) ($this->item->cost_price ?? $this->item->base_rental_price);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            VariantTypeValue::class,
            'inventory_variant_attribute_values',
            'inventory_variant_id',
            'variant_type_value_id',
        )->with('type')->orderBy('variant_type_values.sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
