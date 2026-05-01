<?php

namespace App\Livewire\Inventory;

use App\Models\BookingItem;
use App\Models\InventoryItem;
use App\Models\InventoryVariant;
use App\Models\VariantType;
use App\Models\VariantTypeValue;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Product Detail')]
class Show extends Component
{
    public InventoryItem $item;

    /** @var array<int, int|string> variant_type_id => variant_type_value_id */
    public array $variantAttributes = [];

    public string $variantSku = '';

    public string $variantRentalPrice = '';

    public string $variantCostPrice = '';

    public int $variantStock = 0;

    public int $variantAvailableQty = 0;

    public bool $variantIsActive = true;

    public ?int $editingVariantId = null;

    public ?int $deletingVariantId = null;

    public function mount(InventoryItem $inventoryItem): void
    {
        abort_unless(auth()->user()->can('inventory.view'), 403);
        $this->item = $inventoryItem;
    }

    #[Computed]
    public function variantTypes()
    {
        return VariantType::with(['values' => fn ($q) => $q->ordered()])->ordered()->get();
    }

    #[Computed]
    public function variants()
    {
        return $this->item->variants()->with('attributeValues.type')->orderBy('id')->get();
    }

    #[Computed]
    public function totalBookings(): int
    {
        return BookingItem::where('inventory_item_id', $this->item->id)->count();
    }

    #[Computed]
    public function activeBookings(): int
    {
        return BookingItem::where('inventory_item_id', $this->item->id)
            ->whereHas('booking', fn ($q) => $q->whereIn('status', ['confirmed', 'active']))
            ->count();
    }

    #[Computed]
    public function totalRevenue(): float
    {
        return (float) BookingItem::where('inventory_item_id', $this->item->id)->sum('subtotal');
    }

    #[Computed]
    public function effectiveStock(): int
    {
        $variants = $this->variants;
        if ($variants->isNotEmpty()) {
            return $variants->where('is_active', true)->sum('stock_quantity');
        }

        return $this->item->stock_quantity;
    }

    #[Computed]
    public function effectiveAvailable(): int
    {
        $variants = $this->variants;
        if ($variants->isNotEmpty()) {
            return $variants->where('is_active', true)->sum('available_quantity');
        }

        return $this->item->available_quantity;
    }

    #[Computed]
    public function recentBookings()
    {
        return BookingItem::with(['booking.customer', 'variant'])
            ->where('inventory_item_id', $this->item->id)
            ->latest()
            ->limit(10)
            ->get();
    }

    public function openCreateVariant(): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);
        $this->resetVariantForm();
        $this->js('$flux.modal("variant-form").show()');
    }

    public function openEditVariant(int $id): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        $variant = InventoryVariant::with('attributeValues')->findOrFail($id);
        $this->editingVariantId = $variant->id;

        $this->variantAttributes = $variant->attributeValues
            ->mapWithKeys(fn ($v) => [$v->variant_type_id => $v->id])
            ->toArray();

        $this->variantSku = $variant->sku ?? '';
        $this->variantRentalPrice = $variant->rental_price !== null ? (string) $variant->rental_price : '';
        $this->variantCostPrice = $variant->cost_price !== null ? (string) $variant->cost_price : '';
        $this->variantStock = $variant->stock_quantity;
        $this->variantAvailableQty = $variant->available_quantity;
        $this->variantIsActive = $variant->is_active;

        $this->js('$flux.modal("variant-form").show()');
    }

    public function saveVariant(): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        $selectedValueIds = array_filter(array_values($this->variantAttributes));

        if (empty($selectedValueIds)) {
            $this->addError('variantAttributes', 'Select at least one attribute value for this variation.');

            return;
        }

        $validated = $this->validate([
            'variantSku' => ['nullable', 'string', 'max:100', Rule::unique('inventory_variants', 'sku')->ignore($this->editingVariantId)],
            'variantRentalPrice' => ['nullable', 'numeric', 'min:0'],
            'variantCostPrice' => ['nullable', 'numeric', 'min:0'],
            'variantStock' => ['required', 'integer', 'min:0'],
            'variantAvailableQty' => ['required', 'integer', 'min:0'],
            'variantIsActive' => ['boolean'],
        ]);

        $sku = $validated['variantSku'] ?: null;
        if (! $sku) {
            $sku = $this->generateSku($selectedValueIds);
        }

        $data = [
            'sku' => $sku,
            'rental_price' => $validated['variantRentalPrice'] !== '' && $validated['variantRentalPrice'] !== null ? $validated['variantRentalPrice'] : null,
            'cost_price' => $validated['variantCostPrice'] !== '' && $validated['variantCostPrice'] !== null ? $validated['variantCostPrice'] : null,
            'stock_quantity' => $validated['variantStock'],
            'available_quantity' => $validated['variantAvailableQty'],
            'is_active' => $validated['variantIsActive'],
        ];

        if ($this->editingVariantId) {
            $variant = InventoryVariant::findOrFail($this->editingVariantId);
            $variant->update($data);
        } else {
            $variant = $this->item->variants()->create($data);
        }

        $variant->attributeValues()->sync($selectedValueIds);

        Flux::toast($this->editingVariantId ? 'Variant updated.' : 'Variant created.');
        $this->js('$flux.modal("variant-form").close()');
        $this->resetVariantForm();
        unset($this->variants, $this->effectiveStock, $this->effectiveAvailable);
    }

    public function openDeleteVariant(int $id): void
    {
        abort_unless(auth()->user()->can('inventory.delete'), 403);
        $this->deletingVariantId = $id;
        $this->js('$flux.modal("confirm-delete-variant").show()');
    }

    public function deleteVariant(): void
    {
        abort_unless(auth()->user()->can('inventory.delete'), 403);

        InventoryVariant::findOrFail($this->deletingVariantId)->delete();

        Flux::toast('Variant deleted.');
        $this->deletingVariantId = null;
        $this->js('$flux.modal("confirm-delete-variant").close()');
        unset($this->variants, $this->effectiveStock, $this->effectiveAvailable);
    }

    public function toggleVariantActive(int $id): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        $variant = InventoryVariant::findOrFail($id);
        $variant->update(['is_active' => ! $variant->is_active]);
        unset($this->variants, $this->effectiveStock, $this->effectiveAvailable);
    }

    private function generateSku(array $valueIds): string
    {
        $prefix = $this->item->sku ?? 'ITEM-'.$this->item->id;

        $labels = VariantTypeValue::whereIn('id', $valueIds)
            ->orderBy('sort_order')
            ->pluck('label');

        $suffix = $labels->map(fn ($l) => strtoupper(substr(preg_replace('/\s+/', '', $l), 0, 4)))->join('-');

        $candidate = $prefix.'-'.$suffix;

        if (InventoryVariant::where('sku', $candidate)->where('id', '!=', $this->editingVariantId ?? 0)->exists()) {
            $candidate = $candidate.'-'.rand(100, 999);
        }

        return $candidate;
    }

    private function resetVariantForm(): void
    {
        $this->variantAttributes = [];
        $this->variantSku = '';
        $this->variantRentalPrice = '';
        $this->variantCostPrice = '';
        $this->variantStock = 0;
        $this->variantAvailableQty = 0;
        $this->variantIsActive = true;
        $this->editingVariantId = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.inventory.show');
    }
}
