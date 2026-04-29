<?php

namespace App\Livewire\Reports;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\SupplierInvoice;
use App\Models\SupplierPayment;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public string $activeReport = 'payments';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('reports.view'), 403);
    }

    #[Computed]
    public function totalRevenue(): float
    {
        return (float) Payment::where('is_deposit', false)->sum('amount');
    }

    #[Computed]
    public function totalDeposits(): float
    {
        return (float) Payment::where('is_deposit', true)->sum('amount');
    }

    #[Computed]
    public function totalBookings(): int
    {
        return Booking::count();
    }

    #[Computed]
    public function totalSupplierInvoiced(): float
    {
        return (float) SupplierInvoice::sum('total_amount');
    }

    #[Computed]
    public function totalSupplierPaid(): float
    {
        return (float) SupplierPayment::sum('amount');
    }

    #[Computed]
    public function paymentsByMonth()
    {
        return Payment::query()
            ->selectRaw("strftime('%Y-%m', paid_at) as month, SUM(amount) as total, COUNT(*) as count")
            ->whereNotNull('paid_at')
            ->groupByRaw("strftime('%Y-%m', paid_at)")
            ->orderByRaw("strftime('%Y-%m', paid_at) DESC")
            ->limit(12)
            ->get();
    }

    #[Computed]
    public function bookingsByStatus()
    {
        return Booking::query()
            ->selectRaw('status, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('status')
            ->get();
    }

    #[Computed]
    public function recentPayments()
    {
        return Payment::query()
            ->with(['booking.customer', 'createdBy'])
            ->whereNotNull('paid_at')
            ->orderByDesc('paid_at')
            ->limit(15)
            ->get();
    }

    public function render()
    {
        return view('livewire.reports.index');
    }
}
