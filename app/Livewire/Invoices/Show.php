<?php

namespace App\Livewire\Invoices;

use App\Models\SupplierInvoice;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public SupplierInvoice $invoice;

    public function mount(SupplierInvoice $invoice): void
    {
        abort_unless(auth()->user()->can('payments.view'), 403);
        $this->invoice = $invoice;
    }

    #[Computed]
    public function detail(): SupplierInvoice
    {
        return $this->invoice->load([
            'purchaseOrder.supplier',
            'items.inventoryItem',
            'payments',
            'createdBy',
        ]);
    }

    public function render()
    {
        return view('livewire.invoices.show');
    }
}
