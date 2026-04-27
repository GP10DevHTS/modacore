<?php

namespace App\Livewire\PurchaseOrders;

use App\Models\PurchaseOrder;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    #[Computed]
    public function orders()
    {
        return PurchaseOrder::query()
            ->with('supplier')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('po_number', 'like', "%{$this->search}%")
                    ->orWhereHas('supplier', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function markReceived(int $id): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        $order = PurchaseOrder::with('items')->findOrFail($id);

        if ($order->status !== 'sent') {
            Flux::toast(text: 'Only sent orders can be marked as received.', variant: 'danger');

            return;
        }

        foreach ($order->items as $item) {
            $item->inventoryItem()->increment('stock_quantity', $item->quantity);
        }

        $order->update(['status' => 'received', 'received_at' => now()]);
        unset($this->orders);
        Flux::toast('Purchase order received. Stock updated.');
    }

    public function cancelOrder(int $id): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        $order = PurchaseOrder::findOrFail($id);

        if (in_array($order->status, ['received', 'cancelled'])) {
            Flux::toast(text: 'This order cannot be cancelled.', variant: 'danger');

            return;
        }

        $order->update(['status' => 'cancelled']);
        unset($this->orders);
        Flux::toast('Purchase order cancelled.');
    }

    public function render()
    {
        return view('livewire.purchase-orders.index');
    }
}
