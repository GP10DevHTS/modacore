<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockImportItem extends Model
{
    protected $fillable = ['stock_import_id', 'importable_type', 'importable_id'];

    public function stockImport(): BelongsTo
    {
        return $this->belongsTo(StockImport::class);
    }

    public function importable(): MorphTo
    {
        return $this->morphTo();
    }
}
