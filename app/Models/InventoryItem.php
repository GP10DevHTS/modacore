<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'category_id', 'sku',
        'base_rental_price', 'cost_price', 'stock_quantity', 'available_quantity', 'image_path', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'base_rental_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'available_quantity' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function getEffectiveStockAttribute(): int
    {
        if ($this->relationLoaded('variants') && $this->variants->isNotEmpty()) {
            return $this->variants->where('is_active', true)->sum('stock_quantity');
        }

        $variantStock = $this->variants()->active()->sum('stock_quantity');

        return $variantStock > 0 ? $variantStock : $this->stock_quantity;
    }

    public function variants(): HasMany
    {
        return $this->hasMany(InventoryVariant::class);
    }
}
