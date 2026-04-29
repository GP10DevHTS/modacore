<?php

namespace App\Livewire\Dashboard;

use App\Models\Booking;
use App\Models\Payment;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RevenueSummary extends Component
{
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
    public function monthRevenue(): float
    {
        return (float) Payment::where('is_deposit', false)
            ->whereNotNull('paid_at')
            ->whereRaw("strftime('%Y-%m', paid_at) = strftime('%Y-%m', 'now')")
            ->sum('amount');
    }

    #[Computed]
    public function todayRevenue(): float
    {
        return (float) Payment::where('is_deposit', false)
            ->whereNotNull('paid_at')
            ->whereRaw("date(paid_at) = date('now')")
            ->sum('amount');
    }

    #[Computed]
    public function depositsHeld(): float
    {
        return (float) Payment::where('is_deposit', true)->sum('amount');
    }

    #[Computed]
    public function activeBookingsValue(): float
    {
        return (float) Booking::whereIn('status', ['confirmed', 'active'])->sum('total_amount');
    }

    #[Computed]
    public function avgBookingValue(): float
    {
        return (float) (Booking::whereNotIn('status', ['cancelled'])->avg('total_amount') ?? 0);
    }

    #[Computed]
    public function revenueByMonth()
    {
        return Payment::query()
            ->selectRaw("strftime('%Y-%m', paid_at) as month, SUM(amount) as total, COUNT(*) as cnt")
            ->where('is_deposit', false)
            ->whereNotNull('paid_at')
            ->groupByRaw("strftime('%Y-%m', paid_at)")
            ->orderByRaw("strftime('%Y-%m', paid_at) DESC")
            ->limit(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.revenue-summary');
    }
}
