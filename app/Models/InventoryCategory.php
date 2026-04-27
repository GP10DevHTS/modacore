<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'user_id'];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'category_id');
    }
}
