<?php

namespace App\Livewire\PurchaseOrders;

use App\Enums\OrderStatus;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

class InvoiceForm extends Component
{
    public PurchaseOrder $purchaseOrder;

    public string $invoiceDate = '';

    public string $dueDate = '';

    public string $supplierRef = '';

    public string $notes = '';

    /** @var array<int, array{purchase_order_item_id: int, inventory_item_id: int, item_name: string, ordered: int, pending: int, quantity: int, unit_cost: float}> */
    public array $lines = [];

    public function mount(PurchaseOrder $purchaseOrder): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);
        abort_if($purchaseOrder->order_status === OrderStatus::Cancelled, 403);

        $this->purchaseOrder = $purchaseOrder;
        $this->invoiceDate = now()->format('Y-m-d');

        $this->lines = $purchaseOrder->items->map(fn ($item) => [
            'purchase_order_item_id' => $item->id,
            'inventory_item_id' => $item->inventory_item_id,
            'item_name' => $item->inventoryItem->name,
            'ordered' => $item->quantity,
            'pending' => $item->pendingInvoiceQuantity(),
            'quantity' => $item->pendingInvoiceQuantity(),
            'unit_cost' => (float) $item->unit_cost,
        ])->toArray();
    }

    #[Computed]
    public function totalAmount(): float
    {
        return collect($this->lines)->sum(fn ($l) => $l['quantity'] * floatval(str_replace(',','',$l['unit_cost'])));
    }

    public function save(PurchaseOrderService $service): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        foreach ($this->lines as $index => $line) {
            $this->lines[$index]['unit_cost'] = floatval(str_replace(',', '', trim((string) $line['unit_cost'])));
        }

        $this->validate([
            'invoiceDate' => ['required', 'date'],
            'dueDate' => ['nullable', 'date', 'after_or_equal:invoiceDate'],
            'lines' => ['required', 'array'],
            'lines.*.quantity' => ['required', 'integer', 'min:0'],
            'lines.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $activeLines = array_filter($this->lines, fn ($l) => $l['quantity'] > 0);

        if (empty($activeLines)) {
            Flux::toast(text: 'Enter at least one invoiced quantity.', variant: 'danger');

            return;
        }

        $invoice = $service->createInvoice(
            $this->purchaseOrder,
            $this->invoiceDate,
            array_values($activeLines),
            $this->supplierRef ?: null,
            $this->dueDate ?: null,
            $this->notes ?: null,
        );

        Flux::toast("Invoice {$invoice->invoice_number} created.");
        $this->redirectRoute('purchase-orders.show', $this->purchaseOrder->id);
    }

    public function render()
    {
        return view('livewire.purchase-orders.invoice-form');
    }
}
