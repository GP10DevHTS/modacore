<?php

namespace App\Services;

use App\Models\BookingItem;
use App\Models\InventoryItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
        int $quantity,
        Carbon $hireFrom,
        Carbon $hireTo,
        ?int $excludeBookingId = null,
    ): ?string {
        $availableQuantity = $this->availableQuantity($inventoryItemId, $variantId, $hireFrom, $hireTo, $excludeBookingId);

        if ($availableQuantity <= 0) {
            return 'This item is fully booked for the selected dates.';
        }

        if ($quantity > $availableQuantity) {
            return "Only {$availableQuantity} ".Str::plural('unit', $availableQuantity).' available for the selected dates.';
        }

        return null;
    }

    /**
     * Validate every item in the given list for availability, returning an array
     * of error messages keyed by index. An empty array means all items are free.
     *
     * @param  array<int, array{inventory_item_id: int, inventory_variant_id: int|null, quantity?: int}>  $items
     * @return array<int, string>
     */
    public function validateItems(
        array $items,
        Carbon $hireFrom,
        Carbon $hireTo,
        ?int $excludeBookingId = null,
    ): array {
        $errors = [];
        $requestedQuantities = [];

        foreach ($items as $item) {
            $key = $this->availabilityKey($item['inventory_item_id'], $item['inventory_variant_id'] ?? null);
            $requestedQuantities[$key] = ($requestedQuantities[$key] ?? 0) + max(1, (int) ($item['quantity'] ?? 1));
        }

        foreach ($items as $index => $item) {
            $key = $this->availabilityKey($item['inventory_item_id'], $item['inventory_variant_id'] ?? null);
            $reason = $this->unavailabilityReason(
                $item['inventory_item_id'],
                $item['inventory_variant_id'] ?? null,
                $requestedQuantities[$key],
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

        $bookableQuantity = $this->bookableQuantity($item, $variantId);

        $booked = BookingItem::query()
            ->where('inventory_item_id', $inventoryItemId)
            ->when($variantId, fn ($q) => $q->where('inventory_variant_id', $variantId))
            ->whereIn('status', ['pending', 'checked_out', 'in_cleaning'])
            ->whereHas('booking', function ($q) use ($hireFrom, $hireTo, $excludeBookingId) {
                $q->whereIn('status', ['confirmed', 'active'])
                    ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
                    ->where('hire_from', '<=', $hireTo)
                    ->where('hire_to', '>=', $hireFrom);
            })
            ->sum('quantity');

        $alreadyRemovedFromCurrentAvailability = BookingItem::query()
            ->where('inventory_item_id', $inventoryItemId)
            ->when($variantId, fn ($q) => $q->where('inventory_variant_id', $variantId))
            ->whereIn('status', ['checked_out', 'in_cleaning'])
            ->whereHas('booking', function ($q) use ($hireFrom, $hireTo, $excludeBookingId) {
                $q->whereIn('status', ['confirmed', 'active'])
                    ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
                    ->where('hire_from', '<=', $hireTo)
                    ->where('hire_to', '>=', $hireFrom);
            })
            ->sum('quantity');

        return max(0, $bookableQuantity + (int) $alreadyRemovedFromCurrentAvailability - (int) $booked);
    }

    private function bookableQuantity(InventoryItem $item, ?int $variantId): int
    {
        if ($variantId) {
            return (int) ($item->variants()->active()->whereKey($variantId)->value('available_quantity') ?? 0);
        }

        if ($item->variants()->active()->exists()) {
            return (int) $item->variants()->active()->sum('available_quantity');
        }

        return (int) $item->available_quantity;
    }

    private function availabilityKey(int $inventoryItemId, ?int $variantId): string
    {
        return $inventoryItemId.':'.($variantId ?? 'none');
    }
}
