<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseItem extends Model
{
    protected $fillable = ['category_id', 'name', 'description'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'item_id');
    }
}
