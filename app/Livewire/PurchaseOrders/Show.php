<?php

namespace App\Livewire\PurchaseOrders;

use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public PurchaseOrder $purchaseOrder;

    public function mount(PurchaseOrder $purchaseOrder): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);
        $this->purchaseOrder = $purchaseOrder;
    }

    #[Computed]
    public function order(): PurchaseOrder
    {
        return $this->purchaseOrder->load([
            'supplier', 'items.inventoryItem',
            'goodsReceipts.items.inventoryItem',
            'supplierInvoices.items.inventoryItem', 'supplierInvoices.payments',
            'supplierPayments',
            'auditLogs.user',
            'cancellation',
            'createdBy', 'approvedBy',
        ]);
    }

    public function approvePo(PurchaseOrderService $service): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        $service->approve($this->purchaseOrder);
        unset($this->order);
        Flux::toast('Purchase order approved.');
    }

    public function sendPo(PurchaseOrderService $service): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        $service->send($this->purchaseOrder);
        unset($this->order);
        Flux::toast('Purchase order sent to supplier.');
    }

    public function render()
    {
        return view('livewire.purchase-orders.show');
    }
}
