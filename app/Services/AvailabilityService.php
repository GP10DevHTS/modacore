<?php

namespace App\Services;

use App\Models\BookingItem;
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
        return ! $this->conflictingBookingExists(
            $inventoryItemId, $variantId, $hireFrom, $hireTo, $excludeBookingId,
        );
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
        if ($this->conflictingBookingExists($inventoryItemId, $variantId, $hireFrom, $hireTo, $excludeBookingId)) {
            return 'This item is already booked for the selected dates.';
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

    private function conflictingBookingExists(
        int $inventoryItemId,
        ?int $variantId,
        Carbon $hireFrom,
        Carbon $hireTo,
        ?int $excludeBookingId,
    ): bool {
        return BookingItem::query()
            ->where('inventory_item_id', $inventoryItemId)
            ->when($variantId, fn ($q) => $q->where('inventory_variant_id', $variantId))
            ->whereHas('booking', function ($q) use ($hireFrom, $hireTo, $excludeBookingId) {
                $q->whereIn('status', ['confirmed', 'active'])
                    ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
                    ->where('hire_from', '<=', $hireTo)
                    ->where('hire_to', '>=', $hireFrom);
            })
            ->exists();
    }
}
