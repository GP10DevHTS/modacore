<?php

namespace App\Services;

use App\Models\BookingItem;
use App\Models\InventoryItem;
use Illuminate\Support\Carbon;

class AvailabilityService
{
    /**
     * Check whether an inventory item (optionally a specific variant) is available
     * for the given hire window, excluding a booking already being edited.
     */
    public function isAvailable(
        int $inventoryItemId,
        ?int $variantId,
        Carbon $hireFrom,
        Carbon $hireTo,
        ?int $excludeBookingId = null,
    ): bool {
        return $this->availableQuantity($inventoryItemId, $variantId, $hireFrom, $hireTo, $excludeBookingId) > 0;
    }

    /**
     * Return an error message if the item is unavailable, or null if it is free.
     */
    public function unavailabilityReason(
        int $inventoryItemId,
        ?int $variantId,
        Carbon $hireFrom,
        Carbon $hireTo,
        ?int $excludeBookingId = null,
    ): ?string {
        if ($this->availableQuantity($inventoryItemId, $variantId, $hireFrom, $hireTo, $excludeBookingId) <= 0) {
            return 'This item is fully booked for the selected dates.';
        }

        return null;
    }

    /**
     * Validate every item in the given list for availability, returning an array
     * of error messages keyed by index. An empty array means all items are free.
     *
     * @param  array<int, array{inventory_item_id: int, inventory_variant_id: int|null}>  $items
     * @return array<int, string>
     */
    public function validateItems(
        array $items,
        Carbon $hireFrom,
        Carbon $hireTo,
        ?int $excludeBookingId = null,
    ): array {
        $errors = [];

        foreach ($items as $index => $item) {
            $reason = $this->unavailabilityReason(
                $item['inventory_item_id'],
                $item['inventory_variant_id'] ?? null,
                $hireFrom,
                $hireTo,
                $excludeBookingId,
            );

            if ($reason !== null) {
                $errors[$index] = $reason;
            }
        }

        return $errors;
    }

    /**
     * How many units of this item are not already booked in the given window.
     */
    public function availableQuantity(
        int $inventoryItemId,
        ?int $variantId,
        Carbon $hireFrom,
        Carbon $hireTo,
        ?int $excludeBookingId = null,
    ): int {
        $item = InventoryItem::find($inventoryItemId);

        if (! $item) {
            return 0;
        }

        if ($variantId) {
            $variant = $item->variants()->find($variantId);
            $stock = $variant ? $variant->stock_quantity : 0;
        } else {
            $variantStock = $item->variants()->active()->sum('stock_quantity');
            $stock = $variantStock > 0 ? $variantStock : $item->stock_quantity;
        }

        $booked = BookingItem::query()
            ->where('inventory_item_id', $inventoryItemId)
            ->when($variantId, fn ($q) => $q->where('inventory_variant_id', $variantId))
            ->whereHas('booking', function ($q) use ($hireFrom, $hireTo, $excludeBookingId) {
                $q->whereIn('status', ['confirmed', 'active'])
                    ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
                    ->where('hire_from', '<=', $hireTo)
                    ->where('hire_to', '>=', $hireFrom);
            })
            ->sum('quantity');

        return max(0, $stock - (int) $booked);
    }
}
