<?php

namespace App\Models;

use App\Enums\CancellationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoCancellation extends Model
{
    protected $fillable = [
        'purchase_order_id', 'cancellation_type', 'reason',
        'requires_rts', 'requires_credit_note', 'requires_refund',
        'rts_completed', 'credit_note_completed', 'refund_completed',
        'completed_at', 'cancelled_by',
    ];

    protected function casts(): array
    {
        return [
            'cancellation_type' => CancellationType::class,
            'requires_rts' => 'boolean',
            'requires_credit_note' => 'boolean',
            'requires_refund' => 'boolean',
            'rts_completed' => 'boolean',
            'credit_note_completed' => 'boolean',
            'refund_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function isFullyResolved(): bool
    {
        return (! $this->requires_rts || $this->rts_completed)
            && (! $this->requires_credit_note || $this->credit_note_completed)
            && (! $this->requires_refund || $this->refund_completed);
    }
}
