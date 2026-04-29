<?php

namespace App\Livewire\Invoices;

use App\Models\SupplierInvoice;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('payments.view'), 403);
    }

    #[Computed]
    public function invoices()
    {
        return SupplierInvoice::query()
            ->with(['purchaseOrder.supplier', 'createdBy'])
            ->when($this->search, fn ($q) => $q->where(function ($inner) {
                $inner->where('invoice_number', 'like', "%{$this->search}%")
                    ->orWhere('supplier_invoice_ref', 'like', "%{$this->search}%")
                    ->orWhereHas('purchaseOrder.supplier', fn ($s) => $s->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('payment_status', $this->statusFilter))
            ->latest()
            ->paginate(20);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.invoices.index');
    }
}
