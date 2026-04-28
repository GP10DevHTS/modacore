<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DepositRefund extends Model
{
    protected $fillable = [
        'refund_number', 'booking_id', 'payment_id',
        'amount', 'refunded_at', 'reason', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'refunded_at' => 'date',
        ];
    }

    public static function generateRefundNumber(): string
    {
        $prefix = 'REF-'.now()->format('Ymd').'-';
        $last = static::where('refund_number', 'like', $prefix.'%')
            ->orderByDesc('refund_number')
            ->value('refund_number');

        $sequence = $last ? ((int) Str::afterLast($last, '-')) + 1 : 1;

        return $prefix.str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
