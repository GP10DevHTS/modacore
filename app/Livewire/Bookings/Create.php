<?php

namespace App\Livewire\Bookings;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\CustomerMeasurement;
use App\Models\InventoryItem;
use App\Models\InventoryVariant;
use App\Services\AvailabilityService;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Create extends Component
{
    public ?int $editingBookingId = null;

    public string $customerId = '';

    public string $hireFrom = '';

    public string $hireTo = '';

    public string $notes = '';

    public array $lineItems = [];

    public string $pickerItemId = '';

    public string $pickerVariantId = '';

    public string $pickerCompositionKey = '';

    public array $pickerVariantIds = [];

    public string $pickerQuantity = '1';

    public array $availabilityErrors = [];

    public string $pickerUnitPrice = '';

    public string $searchSku = '';

    public function mount(?Booking $booking = null): void
    {
        if ($booking) {
            $booking->load([
                'items.inventoryItem',
                'items.variant.attributeValues',
            ]);

            if (! $booking->isEditable()) {
                $this->redirectRoute('bookings.show', $booking->id);

                return;
            }

            $this->editingBookingId = $booking->id;
            $this->customerId = (string) $booking->customer_id;
            $this->hireFrom = $booking->hire_from->format('Y-m-d\TH:i');
            $this->hireTo = $booking->hire_to->format('Y-m-d\TH:i');
            $this->notes = $booking->notes ?? '';

            $this->lineItems = $booking->items->map(fn ($line) => [
                'inventory_item_id' => $line->inventory_item_id,
                'inventory_variant_id' => $line->inventory_variant_id,
                'quantity' => $line->quantity,
                'unit_price' => (float) $line->unit_price,
                'subtotal' => (float) $line->subtotal,
                'item_name' => $line->inventoryItem->name,
                'variant_name' => $line->variant?->name,
            ])->toArray();
        }
    }

    #[Computed]
    public function customers()
    {
        return Customer::query()->orderBy('name')->get(['id', 'name', 'phone']);
    }

    #[Computed]
    public function selectedCustomerMeasurements(): ?CustomerMeasurement
    {
        if (! $this->customerId) {
            return null;
        }

        return CustomerMeasurement::where('customer_id', $this->customerId)->first();
    }

    public function updatedCustomerId(): void
    {
        unset($this->selectedCustomerMeasurements);
    }

    #[Computed]
    public function inventoryItems()
    {
        return InventoryItem::query()->active()->orderBy('name')->get(['id', 'name', 'base_rental_price']);
    }

    #[Computed]
    public function selectedItemVariants()
    {
        if (! $this->pickerItemId) {
            return collect();
        }

        return InventoryItem::find($this->pickerItemId)
            ?->variants()
            ->with('attributeValues')
            ->active()
            ->orderBy('label')
            ->orderBy('size')
            ->orderBy('color')
            ->get(['id', 'inventory_item_id', 'size', 'color', 'label', 'composition_key', 'rental_price', 'stock_quantity', 'available_quantity', 'sku'])
            ?? collect();
    }

    #[Computed]
    public function selectedItemCompositions()
    {
        return $this->selectedItemVariants
            ->groupBy(fn ($variant) => $variant->composition_key ?: 'variant-'.$variant->id)
            ->map(function ($variants) {
                $first = $variants->first();
                $compositionKey = $first->composition_key ?: 'variant-'.$first->id;

                return [
                    'id' => $compositionKey,
                    'name' => $first->name.' ('.$variants->where('available_quantity', '>', 0)->count().' available)',
                ];
            })
            ->values();
    }

    #[Computed]
    public function selectedCompositionVariants()
    {
        if (! $this->pickerCompositionKey) {
            return collect();
        }

        return $this->selectedItemVariants
            ->filter(fn ($variant) => ($variant->composition_key ?: 'variant-'.$variant->id) === $this->pickerCompositionKey)
            ->where('available_quantity', '>', 0)
            ->map(fn ($variant) => [
                'id' => $variant->id,
                'name' => $variant->sku.' - '.$variant->name,
            ])
            ->values();
    }

    #[Computed]
    public function totalAmount(): float
    {
        return collect($this->lineItems)->sum('subtotal');
    }

    public function updatedPickerItemId(): void
    {
        $this->pickerVariantId = '';
        $this->pickerCompositionKey = '';
        $this->pickerVariantIds = [];
        $this->pickerUnitPrice = '';
        unset($this->selectedItemVariants, $this->selectedItemCompositions, $this->selectedCompositionVariants);

        if ($this->pickerItemId) {
            $item = InventoryItem::find($this->pickerItemId);
            if ($item) {
                $this->pickerUnitPrice = (string) $item->base_rental_price;
            }
        }
    }

    public function updatedPickerCompositionKey(): void
    {
        $this->pickerVariantId = '';
        $this->pickerVariantIds = [];
        unset($this->selectedCompositionVariants);
    }

    public function updatedPickerVariantId(): void
    {
        if (! $this->pickerVariantId || ! $this->pickerItemId) {
            return;
        }

        $variant = InventoryItem::find($this->pickerItemId)?->variants()->with('attributeValues')->find($this->pickerVariantId);
        if ($variant && $variant->rental_price !== null) {
            $this->pickerUnitPrice = (string) $variant->rental_price;
        }
    }

    public function updatedSearchSku(string $sku): void
    {
        $variant = InventoryVariant::where('sku', $sku)->first();

        if ($variant) {
            $this->reset('pickerCompositionKey', 'pickerItemId', 'pickerVariantIds','pickerUnitPrice');

            Flux::toast(
                'SKU: '.$variant->sku.' - '.$variant->name ." found",
            );

            $this->pickerVariantIds[] = $variant->id;
            $this->pickerCompositionKey = $variant->composition_key;
            $this->pickerItemId = $variant->inventory_item_id;
            $this->pickerUnitPrice = (string) $variant->effectiveRentalPrice();
        } else {
            $this->reset('pickerCompositionKey', 'pickerItemId', 'pickerVariantIds','pickerUnitPrice');
        }
    }

    public function addLineItem(): void
    {
        $this->pickerUnitPrice = (string) floatval(str_replace(',', '', trim($this->pickerUnitPrice)));

        $this->validate([
            'pickerItemId' => ['required', 'exists:inventory_items,id'],
            'pickerQuantity' => ['required', 'integer', 'min:1'],
            'pickerUnitPrice' => ['required', 'numeric', 'min:0'],
            'hireFrom' => ['required', 'date'],
            'hireTo' => ['required', 'date', 'after_or_equal:hireFrom'],
        ]);

        $itemId = (int) $this->pickerItemId;
        $selectedVariantIds = collect($this->pickerVariantIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();
        $variantId = $selectedVariantIds->isEmpty() && $this->pickerVariantId ? (int) $this->pickerVariantId : null;

        if ($selectedVariantIds->isNotEmpty()) {
            foreach ($selectedVariantIds as $selectedVariantId) {
                foreach ($this->lineItems as $existing) {
                    if ($existing['inventory_item_id'] === $itemId && $existing['inventory_variant_id'] === $selectedVariantId) {
                        Flux::toast(text: 'One of the selected units is already in the booking.', variant: 'danger');

                        return;
                    }
                }
            }
        } else {
            foreach ($this->lineItems as $existing) {
                if ($existing['inventory_item_id'] === $itemId && $existing['inventory_variant_id'] === $variantId) {
                    Flux::toast(text: 'This item is already in the booking. Update the quantity instead.', variant: 'danger');

                    return;
                }
            }
        }

        $hireFrom = Carbon::parse($this->hireFrom);
        $hireTo = Carbon::parse($this->hireTo);

        $availability = app(AvailabilityService::class);
        $qty = $selectedVariantIds->isNotEmpty() ? 1 : (int) $this->pickerQuantity;
        $reason = $availability->unavailabilityReason($itemId, $variantId, $qty, $hireFrom, $hireTo, $this->editingBookingId);

        if ($reason) {
            Flux::toast(text: $reason, variant: 'danger');

            return;
        }

        $item = InventoryItem::find($itemId);
        $unitPrice = (float) $this->pickerUnitPrice;

        if ($selectedVariantIds->isNotEmpty()) {
            $item->variants()->with('attributeValues')->whereKey($selectedVariantIds)->get()->each(function ($variant) use ($itemId, $item, $unitPrice) {
                $this->lineItems[] = [
                    'inventory_item_id' => $itemId,
                    'inventory_variant_id' => $variant->id,
                    'quantity' => 1,
                    'unit_price' => $unitPrice,
                    'subtotal' => round($unitPrice, 2),
                    'item_name' => $item->name,
                    'variant_name' => $variant->sku.' - '.$variant->name,
                ];
            });
        } else {
            $this->lineItems[] = [
                'inventory_item_id' => $itemId,
                'inventory_variant_id' => $variantId,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'subtotal' => round($unitPrice * $qty, 2),
                'item_name' => $item->name,
                'variant_name' => $variantId ? ($item->variants()->with('attributeValues')->find($variantId)?->name) : null,
            ];
        }

        unset($this->totalAmount);

        $this->pickerItemId = '';
        $this->pickerVariantId = '';
        $this->pickerCompositionKey = '';
        $this->pickerVariantIds = [];
        $this->pickerQuantity = '1';
        $this->pickerUnitPrice = '';
        unset($this->selectedItemVariants, $this->selectedItemCompositions, $this->selectedCompositionVariants, $this->searchSku);
    }

    public function removeLineItem(int $index): void
    {
        array_splice($this->lineItems, $index, 1);
        unset($this->totalAmount);
    }

    public function updateLineQuantity(int $index, int $quantity): void
    {
        if ($quantity < 1) {
            return;
        }

        if (! isset($this->lineItems[$index])) {
            return;
        }

        if ($this->hireFrom && $this->hireTo) {
            $line = $this->lineItems[$index];
            $reason = app(AvailabilityService::class)->unavailabilityReason(
                $line['inventory_item_id'],
                $line['inventory_variant_id'] ?? null,
                $quantity,
                Carbon::parse($this->hireFrom),
                Carbon::parse($this->hireTo),
                $this->editingBookingId,
            );

            if ($reason) {
                Flux::toast(text: $reason, variant: 'danger');

                return;
            }
        }

        $this->lineItems[$index]['quantity'] = $quantity;
        $this->lineItems[$index]['subtotal'] = round($this->lineItems[$index]['unit_price'] * $quantity, 2);
        unset($this->totalAmount);
    }

    public function updateLinePrice(int $index, string $price): void
    {
        $price = str_replace(',', '', trim($price));
        $unitPrice = max(0, (float) $price);
        $this->lineItems[$index]['unit_price'] = $unitPrice;
        $this->lineItems[$index]['subtotal'] = round($unitPrice * $this->lineItems[$index]['quantity'], 2);
        unset($this->totalAmount);
    }



    public function save(string $status = 'draft'): void
    {
        abort_unless(auth()->user()->can($this->editingBookingId ? 'bookings.edit' : 'bookings.create'), 403);

        $this->validate([
            'customerId' => ['required', 'exists:customers,id'],
            'hireFrom' => ['required', 'date'],
            'hireTo' => ['required', 'date', 'after_or_equal:hireFrom'],
            'notes' => ['nullable', 'string'],
            'lineItems' => ['required', 'array', 'min:1'],
        ]);

        if (count($this->lineItems) === 0) {
            Flux::toast(text: 'Please add at least one item to the booking.', variant: 'danger');

            return;
        }

        $hireFrom = Carbon::parse($this->hireFrom);
        $hireTo = Carbon::parse($this->hireTo);

        $availability = app(AvailabilityService::class);
        $errors = $availability->validateItems(
            array_map(fn ($item) => [
                'inventory_item_id' => $item['inventory_item_id'],
                'inventory_variant_id' => $item['inventory_variant_id'],
                'quantity' => $item['quantity'],
            ], $this->lineItems),
            $hireFrom,
            $hireTo,
            $this->editingBookingId,
        );

        if (! empty($errors)) {
            $this->availabilityErrors = $errors;
            Flux::toast(text: 'Some items are unavailable for the selected dates.', variant: 'danger');

            return;
        }

        $this->availabilityErrors = [];
        $total = collect($this->lineItems)->sum('subtotal');

        if ($this->editingBookingId) {
            $booking = Booking::findOrFail($this->editingBookingId);
            $booking->update([
                'customer_id' => $this->customerId,
                'hire_from' => $hireFrom,
                'hire_to' => $hireTo,
                'status' => $status,
                'total_amount' => $total,
                'notes' => $this->notes ?: null,
            ]);

            $booking->items()->delete();
        } else {
            $booking = Booking::create([
                'booking_number' => Booking::generateBookingNumber(),
                'customer_id' => $this->customerId,
                'hire_from' => $hireFrom,
                'hire_to' => $hireTo,
                'status' => $status,
                'total_amount' => $total,
                'notes' => $this->notes ?: null,
                'created_by' => auth()->id(),
            ]);
        }

        foreach ($this->lineItems as $lineItem) {
            $booking->items()->create([
                'inventory_item_id' => $lineItem['inventory_item_id'],
                'inventory_variant_id' => $lineItem['inventory_variant_id'],
                'quantity' => $lineItem['quantity'],
                'unit_price' => $lineItem['unit_price'],
                'subtotal' => $lineItem['subtotal'],
            ]);
        }

        Flux::toast($this->editingBookingId ? 'Booking updated.' : 'Booking created.');

        $this->redirectRoute('bookings.show', $booking->id);
    }

    public function render()
    {
        return view('livewire.bookings.create');
    }
}
