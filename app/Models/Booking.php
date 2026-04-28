<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_number', 'customer_id', 'hire_from', 'hire_to',
        'status', 'total_amount', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'hire_from' => 'datetime',
            'hire_to' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    public static function generateBookingNumber(): string
    {
        $prefix = 'BK-'.now()->format('Ymd').'-';
        $last = static::where('booking_number', 'like', $prefix.'%')
            ->orderByDesc('booking_number')
            ->value('booking_number');

        $sequence = $last ? ((int) Str::afterLast($last, '-')) + 1 : 1;

        return $prefix.str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['confirmed', 'active']);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term) {
            $q->where('booking_number', 'like', "%{$term}%")
                ->orWhereHas('customer', fn (Builder $c) => $c->where('name', 'like', "%{$term}%"));
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function depositRefunds(): HasMany
    {
        return $this->hasMany(DepositRefund::class);
    }

    public function totalDepositsHeld(): float
    {
        $deposited = (float) $this->payments()->where('is_deposit', true)->sum('amount');
        $refunded = (float) $this->depositRefunds()->sum('amount');

        return max(0, $deposited - $refunded);
    }

    public function getHireDurationAttribute(): int
    {
        return $this->hire_from->diffInDays($this->hire_to) + 1;
    }

    public function getAmountPaidAttribute(): float
    {
        return (float) $this->payments()->where('is_deposit', false)->sum('amount');
    }

    public function getDepositPaidAttribute(): float
    {
        return (float) $this->payments()->where('is_deposit', true)->sum('amount');
    }

    public function getBalanceDueAttribute(): float
    {
        return max(0, (float) $this->total_amount - $this->amount_paid - $this->deposit_paid);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'confirmed']);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['draft', 'confirmed']);
    }
}
