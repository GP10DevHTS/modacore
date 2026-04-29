<?php

namespace App\Livewire\Analytics;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Payment;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()->can('reports.view'), 403);
    }

    #[Computed]
    public function totalBookings(): int
    {
        return Booking::count();
    }

    #[Computed]
    public function activeBookings(): int
    {
        return Booking::whereIn('status', ['confirmed', 'active'])->count();
    }

    #[Computed]
    public function completedBookings(): int
    {
        return Booking::where('status', 'completed')->count();
    }

    #[Computed]
    public function totalRevenue(): float
    {
        return (float) Payment::where('is_deposit', false)->sum('amount');
    }

    #[Computed]
    public function totalCustomers(): int
    {
        return Customer::count();
    }

    #[Computed]
    public function activeInventoryItems(): int
    {
        return InventoryItem::where('is_active', true)->count();
    }

    #[Computed]
    public function bookingsByStatus(): array
    {
        return Booking::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    #[Computed]
    public function revenueByMonth()
    {
        return Payment::query()
            ->selectRaw("strftime('%Y-%m', paid_at) as month, SUM(amount) as total, COUNT(*) as count")
            ->where('is_deposit', false)
            ->whereNotNull('paid_at')
            ->groupByRaw("strftime('%Y-%m', paid_at)")
            ->orderByRaw("strftime('%Y-%m', paid_at) DESC")
            ->limit(6)
            ->get();
    }

    #[Computed]
    public function topItems()
    {
        return InventoryItem::query()
            ->select('inventory_items.*')
            ->selectRaw('(SELECT COUNT(*) FROM booking_items WHERE booking_items.inventory_item_id = inventory_items.id) as rental_count')
            ->orderByDesc('rental_count')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function recentBookings()
    {
        return Booking::query()
            ->with('customer')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.analytics.dashboard');
    }
}
