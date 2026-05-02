<?php

namespace App\Livewire\PurchaseOrders;

use App\Enums\OrderStatus;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\VariantType;
use App\Services\InventorySkuService;
use App\Services\PurchaseOrderService;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Create extends Component
{
    public ?int $editingOrderId = null;

    public string $supplierId = '';

    public string $expectedAt = '';

    public string $notes = '';

    public array $lineItems = [];

    public string $pickerItemId = '';

    public string $pickerVariantId = '';

    public array $pickerVariantAttributes = [];

    public string $pickerQuantity = '1';

    public string $pickerUnitCost = '';

    public function mount(?PurchaseOrder $purchaseOrder = null): void
    {
        abort_unless(auth()->user()->can('inventory.create'), 403);

        if ($purchaseOrder->exists ?? false) {
            if (! $purchaseOrder->order_status->canEdit()) {
                $this->redirectRoute('purchase-orders.show', $purchaseOrder->id);

                return;
            }

            $this->editingOrderId = $purchaseOrder->id;
            $this->supplierId = (string) $purchaseOrder->supplier_id;
            $this->expectedAt = $purchaseOrder->expected_at?->format('Y-m-d') ?? '';
            $this->notes = $purchaseOrder->notes ?? '';
            $this->lineItems = $purchaseOrder->items->map(fn ($item) => [
                'inventory_item_id' => $item->inventory_item_id,
                'inventory_variant_id' => $item->inventory_variant_id,
                'variant_value_ids' => $item->variant_value_ids ?? [],
                'variant_composition_key' => $item->variant_composition_key,
                'variant_composition_label' => $item->variant_composition_label,
                'quantity' => $item->quantity,
                'unit_cost' => (float) $item->unit_cost,
                'subtotal' => (float) $item->subtotal,
                'item_name' => $item->inventoryItem->name,
                'variant_name' => $item->variant_composition_label ?? $item->variant?->name,
            ])->toArray();
        }
    }

    #[Computed]
    public function suppliers()
    {
        return Supplier::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function inventoryItems()
    {
        return InventoryItem::query()->orderBy('name')->get(['id', 'name', 'base_rental_price']);
    }

    #[Computed]
    public function totalAmount(): float
    {
        return collect($this->lineItems)->sum('subtotal');
    }

    #[Computed]
    public function selectedItemVariants()
    {
        if (! $this->pickerItemId) {
            return collect();
        }

        return InventoryItem::find($this->pickerItemId)?->variants()->with('attributeValues')->active()->orderBy('label')->orderBy('size')->orderBy('color')->get(['id', 'inventory_item_id', 'size', 'color', 'label', 'composition_key', 'rental_price', 'cost_price']) ?? collect();
    }

    #[Computed]
    public function variantTypes()
    {
        return VariantType::with(['values' => fn ($q) => $q->ordered()])->ordered()->get();
    }

    public function updatedPickerItemId(): void
    {
        $this->pickerVariantId = '';
        $this->pickerVariantAttributes = [];
        unset($this->selectedItemVariants);
        $item = InventoryItem::find($this->pickerItemId);
        $this->pickerUnitCost = $item ? (string) ($item->cost_price ?? '') : '';
    }

    public function updatedPickerVariantId(): void
    {
        if (! $this->pickerVariantId || ! $this->pickerItemId) {
            return;
        }

        $variant = InventoryItem::find($this->pickerItemId)?->variants()->find($this->pickerVariantId);
        if ($variant) {
            $effectiveCost = $variant->cost_price ?? $variant->item->cost_price ?? null;
            if ($effectiveCost !== null) {
                $this->pickerUnitCost = (string) $effectiveCost;
            }
        }
    }

    public function updateLineCost(int $index, string $cost): void
    {
        $cost = str_replace(',', '', trim($cost));
        $unitCost = max(0, (float) $cost);
        $this->lineItems[$index]['unit_cost'] = $unitCost;
        $this->lineItems[$index]['subtotal'] = round($unitCost * $this->lineItems[$index]['quantity'], 2);
        unset($this->totalAmount);
    }

    public function addLineItem(InventorySkuService $skuService): void
    {
        $this->pickerUnitCost = (string) floatval(str_replace(',', '', trim($this->pickerUnitCost)));

        $this->validate([
            'pickerItemId' => ['required', 'exists:inventory_items,id'],
            'pickerQuantity' => ['required', 'integer', 'min:1'],
            'pickerUnitCost' => ['required', 'numeric', 'min:0'],
        ]);

        $itemId = (int) $this->pickerItemId;
        $variantId = $this->pickerVariantId ? (int) $this->pickerVariantId : null;
        $selectedValueIds = array_values(array_filter($this->pickerVariantAttributes));
        $compositionKey = ! empty($selectedValueIds) ? $skuService->compositionKey($selectedValueIds) : null;
        $compositionLabel = ! empty($selectedValueIds) ? $skuService->compositionLabel($selectedValueIds) : null;

        foreach ($this->lineItems as $existing) {
            if (
                $existing['inventory_item_id'] === $itemId
                && ($existing['inventory_variant_id'] ?? null) === $variantId
                && ($existing['variant_composition_key'] ?? null) === $compositionKey
            ) {
                Flux::toast(text: 'Item already in order. Update the quantity instead.', variant: 'danger');

                return;
            }
        }

        $item = InventoryItem::find($itemId);
        $qty = (int) $this->pickerQuantity;
        $unitCost = (float) $this->pickerUnitCost;

        $this->lineItems[] = [
            'inventory_item_id' => $itemId,
            'inventory_variant_id' => $variantId,
            'variant_value_ids' => array_map('intval', $selectedValueIds),
            'variant_composition_key' => $compositionKey,
            'variant_composition_label' => $compositionLabel,
            'quantity' => $qty,
            'unit_cost' => $unitCost,
            'subtotal' => round($unitCost * $qty, 2),
            'item_name' => $item->name,
            'variant_name' => $compositionLabel ?: ($variantId ? ($item->variants()->with('attributeValues')->find($variantId)?->name) : null),
        ];

        unset($this->totalAmount, $this->selectedItemVariants);
        $this->pickerItemId = '';
        $this->pickerVariantId = '';
        $this->pickerVariantAttributes = [];
        $this->pickerQuantity = '1';
        $this->pickerUnitCost = '';
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

        $this->lineItems[$index]['quantity'] = $quantity;
        $this->lineItems[$index]['subtotal'] = round($this->lineItems[$index]['unit_cost'] * $quantity, 2);
        unset($this->totalAmount);
    }

    public function save(string $action = 'draft', ?PurchaseOrderService $poService = null): void
    {
        abort_unless(auth()->user()->can($this->editingOrderId ? 'inventory.edit' : 'inventory.create'), 403);

        $this->validate([
            'supplierId' => ['required', 'exists:suppliers,id'],
            'expectedAt' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'lineItems' => ['required', 'array', 'min:1'],
        ]);

        $total = collect($this->lineItems)->sum('subtotal');

        if ($this->editingOrderId) {
            $order = PurchaseOrder::findOrFail($this->editingOrderId);
            $order->update([
                'supplier_id' => $this->supplierId,
                'total_amount' => $total,
                'expected_at' => $this->expectedAt ?: null,
                'notes' => $this->notes ?: null,
            ]);
            $order->items()->delete();
        } else {
            $order = PurchaseOrder::create([
                'po_number' => PurchaseOrder::generatePoNumber(),
                'supplier_id' => $this->supplierId,
                'order_status' => OrderStatus::Draft,
                'total_amount' => $total,
                'expected_at' => $this->expectedAt ?: null,
                'notes' => $this->notes ?: null,
                'created_by' => auth()->id(),
            ]);
        }

        foreach ($this->lineItems as $lineItem) {
            $order->items()->create([
                'inventory_item_id' => $lineItem['inventory_item_id'],
                'inventory_variant_id' => $lineItem['inventory_variant_id'] ?? null,
                'variant_value_ids' => $lineItem['variant_value_ids'] ?? null,
                'variant_composition_key' => $lineItem['variant_composition_key'] ?? null,
                'variant_composition_label' => $lineItem['variant_composition_label'] ?? null,
                'quantity' => $lineItem['quantity'],
                'unit_cost' => $lineItem['unit_cost'],
                'subtotal' => $lineItem['subtotal'],
            ]);
        }

        if ($action === 'sent' && $poService) {
            $poService->send($order);
        } elseif ($action === 'approved' && $poService) {
            $poService->approve($order);
        }

        Flux::toast($this->editingOrderId ? 'Purchase order updated.' : 'Purchase order created.');
        $this->redirectRoute('purchase-orders.show', $order->id);
    }

    public function render()
    {
        return view('livewire.purchase-orders.create');
    }
}
