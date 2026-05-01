<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerMeasurement extends Model
{
    protected $fillable = [
        'customer_id',
        'chest',
        'waist',
        'hips',
        'shoulder_width',
        'sleeve_length',
        'inseam',
        'neck',
        'height',
    ];

    protected $casts = [
        'chest' => 'decimal:2',
        'waist' => 'decimal:2',
        'hips' => 'decimal:2',
        'shoulder_width' => 'decimal:2',
        'sleeve_length' => 'decimal:2',
        'inseam' => 'decimal:2',
        'neck' => 'decimal:2',
        'height' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
