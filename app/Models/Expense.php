<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expense_number',
        'item_id',
        'title',
        'description',
        'amount',
        'expense_date',
        'reference',
        'notes',
        'payment_status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount' => 'decimal:2',
            'payment_status' => PaymentStatus::class,
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function item(): BelongsTo
    {
        return $this->belongsTo(ExpenseItem::class, 'item_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ExpensePayment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Financial helpers ────────────────────────────────────────────────────

    public function totalPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function balance(): float
    {
        return max(0, (float) $this->amount - $this->totalPaid());
    }

    public function recomputePaymentStatus(): void
    {
        $totalPaid = $this->payments()->sum('amount');

        $status = match (true) {
            $totalPaid <= 0 => PaymentStatus::Unpaid,
            $totalPaid >= (float) $this->amount => PaymentStatus::FullyPaid,
            default => PaymentStatus::PartiallyPaid,
        };

        $this->update(['payment_status' => $status]);
    }

    // ── Number generation ────────────────────────────────────────────────────

    public static function nextNumber(): string
    {
        $year = now()->year;
        $count = static::withTrashed()->whereYear('created_at', $year)->count() + 1;

        return sprintf('BILL-%d-%04d', $year, $count);
    }
}
