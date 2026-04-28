<?php

namespace App\Livewire\PurchaseOrders;

use App\Enums\ClosureStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\PurchaseOrder;
use App\Models\SupplierInvoice;
use App\Models\SupplierPayment;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProcurementDashboard extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);
    }

    #[Computed]
    public function totalPoValue(): float
    {
        return (float) PurchaseOrder::whereNotIn('order_status', [OrderStatus::Cancelled->value])->sum('total_amount');
    }

    #[Computed]
    public function totalInvoiced(): float
    {
        return (float) SupplierInvoice::whereNull('deleted_at')->sum('total_amount');
    }

    #[Computed]
    public function totalPaid(): float
    {
        return (float) SupplierPayment::sum('amount');
    }

    #[Computed]
    public function totalOutstanding(): float
    {
        return max(0, $this->totalInvoiced - $this->totalPaid);
    }

    #[Computed]
    public function uninvoicedValue(): float
    {
        return max(0, $this->totalPoValue - $this->totalInvoiced);
    }

    #[Computed]
    public function overdueInvoices()
    {
        return SupplierInvoice::query()
            ->with('purchaseOrder.supplier')
            ->whereNull('deleted_at')
            ->whereNotIn('payment_status', [PaymentStatus::FullyPaid->value])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->get();
    }

    #[Computed]
    public function recentOrders()
    {
        return PurchaseOrder::query()
            ->with('supplier')
            ->latest()
            ->limit(8)
            ->get();
    }

    #[Computed]
    public function ordersByStatus(): array
    {
        $counts = PurchaseOrder::query()
            ->selectRaw('order_status, count(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status')
            ->toArray();

        return $counts;
    }

    #[Computed]
    public function openOrders(): int
    {
        return PurchaseOrder::where('closure_status', ClosureStatus::Open->value)
            ->whereNotIn('order_status', [OrderStatus::Cancelled->value])
            ->count();
    }

    public function render()
    {
        return view('livewire.purchase-orders.procurement-dashboard');
    }
}
