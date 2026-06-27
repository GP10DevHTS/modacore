<?php

namespace App\Livewire;

use App\Models\BookingItem;
use App\Models\InventoryItem;
use App\Models\InventoryVariant;
use Livewire\Attributes\Computed;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

    public bool $showDropdown = false;

    #[Computed]
    public function results(): array
    {
        if (strlen(trim($this->query)) < 1) {
            return [];
        }

        $term = $this->query;

        // Prioritize variants - search by SKU first
        $variants = InventoryVariant::query()
            ->with('item')
            ->where('sku', 'like', "%{$term}%")
            ->orWhere('label', 'like', "%{$term}%")
            ->active()
            ->limit(8)
            ->get()
            ->map(fn ($v) => $this->buildResult($v))
            ->values()
            ->toArray();

        $variantCount = count($variants);

        // Only search items if we have room (max 10 total results)
        $items = collect();
        if ($variantCount < 10) {
            $items = InventoryItem::query()
                ->where('name', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->active()
                ->limit(10 - $variantCount)
                ->get()
                ->map(fn ($i) => $this->buildResult(null, $i))
                ->values();
        }

        return array_merge($variants, $items->toArray());
    }

    private function buildResult(?InventoryVariant $variant, ?InventoryItem $item = null): array
    {
        $inventoryItem = $variant?->item ?? $item;
        $itemName = $inventoryItem?->name ?? 'Unknown';
        $sku = $variant?->sku ?? $inventoryItem?->sku;
        $hasVariant = $variant !== null;

        // Determine display name: "ItemName (VariantLabel)" if variant has label, else just item name
        $displayName = $hasVariant && $variant->label
            ? "{$itemName} ({$variant->label})"
            : $itemName;

        // Rental price
        $rentalPrice = $hasVariant
            ? $variant->effectiveRentalPrice()
            : ($inventoryItem?->base_rental_price ?? 0);

        // Determine where this item/variant is
        $locationInfo = $this->determineLocation(
            $inventoryItem?->id,
            $variant?->id,
            $hasVariant
        );

        // Check if on a booking show page
        $onBookingPage = request()->routeIs('bookings.show');
        $bookingId = $onBookingPage ? request()->route('booking')?->id : null;

        return [
            'id' => $variant?->id ?? $inventoryItem?->id,
            'type' => $hasVariant ? 'variant' : 'item',
            'display_name' => $displayName,
            'sku' => $sku,
            'inventory_item_id' => $inventoryItem?->id,
            'inventory_variant_id' => $variant?->id,
            'rental_price' => (float) $rentalPrice,
            'location' => $locationInfo,
            'on_booking_page' => $onBookingPage,
            'booking_id' => $bookingId,
        ];
    }

    private function determineLocation(?int $itemId, ?int $variantId, bool $hasVariant): array
    {
        // Check active booking items for this item/variant
        $checkedOutItems = BookingItem::query()
            ->when($hasVariant && $variantId, fn ($q) => $q->where('inventory_variant_id', $variantId))
            ->when(! $hasVariant || ! $variantId, fn ($q) => $q->where('inventory_item_id', $itemId))
            ->whereIn('status', ['checked_out', 'in_cleaning'])
            ->with('booking.customer')
            ->get();

        $checkedOut = $checkedOutItems->where('status', 'checked_out');
        $inCleaning = $checkedOutItems->where('status', 'in_cleaning');

        // Check available quantity
        if ($hasVariant && $variantId) {
            $variant = InventoryVariant::find($variantId);
            $isAvailable = $variant && $variant->available_quantity > 0;
            $availableQty = $variant?->available_quantity ?? 0;
        } else {
            $item = InventoryItem::find($itemId);
            $isAvailable = $item && $item->available_quantity > 0;
            $availableQty = $item?->available_quantity ?? 0;
        }

        $status = 'available';
        $label = "In Stock ({$availableQty} available)";
        $color = 'emerald';

        if ($checkedOut->isNotEmpty()) {
            $status = 'checked_out';
            $booking = $checkedOut->first()->booking;
            $label = 'Checked Out — ' . ($booking?->booking_number ?? 'N/A') . ' (' . ($booking?->customer?->name ?? 'N/A') . ')';
            $color = 'amber';

            if ($inCleaning->isNotEmpty()) {
                $label .= ' | Some in Cleaning';
                $color = 'orange';
            }
        } elseif ($inCleaning->isNotEmpty()) {
            $status = 'in_cleaning';
            $label = 'In Cleaning';
            $color = 'sky';
        }

        return [
            'status' => $status,
            'label' => $label,
            'color' => $color,
            'checked_out_count' => $checkedOut->count(),
            'in_cleaning_count' => $inCleaning->count(),
        ];
    }

    public function updatedQuery(): void
    {
        $this->showDropdown = strlen(trim($this->query)) >= 1;
    }

    public function selectResult(int $itemId, ?int $variantId): void
    {
        $this->showDropdown = false;
        $this->query = '';

        $onBookingPage = request()->routeIs('bookings.show');
        $booking = $onBookingPage ? request()->route('booking') : null;

        if ($onBookingPage && $booking) {
            $this->redirect(route('bookings.edit', $booking) . '?item=' . $itemId . ($variantId ? '&variant=' . $variantId : ''));
        } else {
            $this->redirect(route('bookings.create') . '?item=' . $itemId . ($variantId ? '&variant=' . $variantId : ''));
        }
    }

    public function hideDropdown(): void
    {
        $this->showDropdown = false;
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
