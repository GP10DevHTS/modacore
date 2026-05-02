<?php

namespace App\Models;

use App\Services\InventorySkuService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'user_id', 'code'];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'category_id');
    }

    protected static function booted()
    {
        static::creating(function ($category) {
            $category->code ??= app(InventorySkuService::class)->nextCategoryCode();
        });
    }
}
