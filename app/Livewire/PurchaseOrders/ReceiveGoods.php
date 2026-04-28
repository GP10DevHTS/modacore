<?php

namespace App\Livewire\PurchaseOrders;

use App\Enums\OrderStatus;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ReceiveGoods extends Component
{
    public PurchaseOrder $purchaseOrder;

    public string $receivedDate = '';

    public string $notes = '';

    /** @var array<int, array{purchase_order_item_id: int, inventory_item_id: int, item_name: string, ordered: int, pending: int, quantity_received: int, notes: string}> */
    public array $lines = [];

    public function mount(PurchaseOrder $purchaseOrder): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);
        abort_if($purchaseOrder->order_status !== OrderStatus::Sent, 403);

        $this->purchaseOrder = $purchaseOrder;
        $this->receivedDate = now()->format('Y-m-d');

        $this->lines = $purchaseOrder->items->map(fn ($item) => [
            'purchase_order_item_id' => $item->id,
            'inventory_item_id' => $item->inventory_item_id,
            'item_name' => $item->inventoryItem->name,
            'ordered' => $item->quantity,
            'pending' => $item->pendingReceiptQuantity(),
            'quantity_received' => $item->pendingReceiptQuantity(),
            'notes' => '',
        ])->toArray();
    }

    #[Computed]
    public function totalReceiving(): int
    {
        return collect($this->lines)->sum('quantity_received');
    }

    public function save(PurchaseOrderService $service): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        $this->validate([
            'receivedDate' => ['required', 'date'],
            'lines' => ['required', 'array'],
            'lines.*.quantity_received' => ['required', 'integer', 'min:0'],
        ]);

        $activeLines = array_filter($this->lines, fn ($l) => $l['quantity_received'] > 0);

        if (empty($activeLines)) {
            Flux::toast(text: 'Enter at least one received quantity.', variant: 'danger');

            return;
        }

        $grn = $service->receiveGoods($this->purchaseOrder, $this->receivedDate, array_values($activeLines), $this->notes ?: null);

        Flux::toast("GRN {$grn->grn_number} recorded successfully.");
        $this->redirectRoute('purchase-orders.show', $this->purchaseOrder->id);
    }

    public function render()
    {
        return view('livewire.purchase-orders.receive-goods');
    }
}
