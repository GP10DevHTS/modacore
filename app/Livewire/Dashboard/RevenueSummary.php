<?php

namespace App\Livewire\Dashboard;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
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
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');
    }

    #[Computed]
    public function todayRevenue(): float
    {
        return (float) Payment::where('is_deposit', false)
            ->whereNotNull('paid_at')
            ->whereDate('paid_at', today())
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
        $fmt = $this->monthExpr('paid_at');

        return Payment::query()
            ->selectRaw("{$fmt} as month, SUM(amount) as total, COUNT(*) as cnt")
            ->where('is_deposit', false)
            ->whereNotNull('paid_at')
            ->groupByRaw($fmt)
            ->orderByRaw("{$fmt} DESC")
            ->limit(6)
            ->get();
    }

    private function monthExpr(string $column): string
    {
        return match (DB::getDriverName()) {
            'mysql', 'mariadb' => "DATE_FORMAT({$column}, '%Y-%m')",
            default => "strftime('%Y-%m', {$column})",
        };
    }

    public function render()
    {
        return view('livewire.dashboard.revenue-summary');
    }
}
