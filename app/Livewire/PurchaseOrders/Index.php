<?php

namespace App\Livewire\PurchaseOrders;

use App\Enums\CancellationType;
use App\Models\PurchaseOrder;
use App\Services\CancellationService;
use Flux\Flux;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $cancelReason = '';

    public ?int $cancellingOrderId = null;

    #[Computed]
    public function orders()
    {
        return PurchaseOrder::query()
            ->with('supplier')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('po_number', 'like', "%{$this->search}%")
                    ->orWhereHas('supplier', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('order_status', $this->statusFilter))
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

    public function confirmCancel(int $id): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        $this->cancellingOrderId = $id;
        $this->cancelReason = '';
        Flux::modal('cancel-order')->show();
    }

    public function cancelOrder(CancellationService $service): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        $this->validate(['cancelReason' => ['required', 'string', 'min:5']]);

        $order = PurchaseOrder::findOrFail($this->cancellingOrderId);

        try {
            $cancellation = $service->initiate($order, $this->cancelReason);
        } catch (ValidationException $e) {
            Flux::toast(text: $e->getMessage(), variant: 'danger');

            return;
        }

        Flux::modal('cancel-order')->close();
        unset($this->orders);

        $type = $cancellation->cancellation_type;
        $msg = match ($type) {
            default => 'Order cancelled.',
            CancellationType::WithReturnToSupplier => 'Cancellation initiated. A Return to Supplier is required.',
            CancellationType::WithCreditNote => 'Cancellation initiated. A Credit Note from the supplier is required.',
            CancellationType::WithRefund => 'Cancellation initiated. A Refund must be processed.',
        };

        Flux::toast($msg);
    }

    public function render()
    {
        return view('livewire.purchase-orders.index');
    }
}
