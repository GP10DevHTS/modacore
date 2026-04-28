<?php

namespace App\Livewire\PurchaseOrders;

use App\Enums\OrderStatus;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
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
                'quantity' => $item->quantity,
                'unit_cost' => (float) $item->unit_cost,
                'subtotal' => (float) $item->subtotal,
                'item_name' => $item->inventoryItem->name,
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

    public function updatedPickerItemId(): void
    {
        $item = InventoryItem::find($this->pickerItemId);
        $this->pickerUnitCost = $item ? (string) $item->base_rental_price : '';
    }

    public function addLineItem(): void
    {
        $this->validate([
            'pickerItemId' => ['required', 'exists:inventory_items,id'],
            'pickerQuantity' => ['required', 'integer', 'min:1'],
            'pickerUnitCost' => ['required', 'numeric', 'min:0'],
        ]);

        $itemId = (int) $this->pickerItemId;

        foreach ($this->lineItems as $existing) {
            if ($existing['inventory_item_id'] === $itemId) {
                Flux::toast(text: 'Item already in order. Update the quantity instead.', variant: 'danger');

                return;
            }
        }

        $item = InventoryItem::find($itemId);
        $qty = (int) $this->pickerQuantity;
        $unitCost = (float) $this->pickerUnitCost;

        $this->lineItems[] = [
            'inventory_item_id' => $itemId,
            'quantity' => $qty,
            'unit_cost' => $unitCost,
            'subtotal' => round($unitCost * $qty, 2),
            'item_name' => $item->name,
        ];

        unset($this->totalAmount);
        $this->pickerItemId = '';
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
