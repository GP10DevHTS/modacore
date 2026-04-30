<?php

namespace App\Livewire\Reports;

use App\Models\Booking;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpensePayment;
use App\Models\Payment;
use App\Models\SupplierInvoice;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;
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
        $fmt = match (DB::getDriverName()) {
            'mysql', 'mariadb' => "DATE_FORMAT(paid_at, '%Y-%m')",
            default => "strftime('%Y-%m', paid_at)",
        };

        return Payment::query()
            ->selectRaw("{$fmt} as month, SUM(amount) as total, COUNT(*) as count")
            ->whereNotNull('paid_at')
            ->groupByRaw($fmt)
            ->orderByRaw("{$fmt} DESC")
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

    #[Computed]
    public function totalExpenses(): float
    {
        return (float) Expense::sum('amount');
    }

    #[Computed]
    public function totalOutstandingExpenses(): float
    {
        return max(0, (float) Expense::sum('amount') - (float) ExpensePayment::sum('amount'));
    }

    #[Computed]
    public function expensesByMonth()
    {
        $fmt = match (DB::getDriverName()) {
            'mysql', 'mariadb' => "DATE_FORMAT(expense_date, '%Y-%m')",
            default => "strftime('%Y-%m', expense_date)",
        };

        return Expense::query()
            ->selectRaw("{$fmt} as month, SUM(amount) as total_billed, COUNT(*) as count")
            ->groupByRaw($fmt)
            ->orderByRaw("{$fmt} DESC")
            ->limit(24)
            ->get();
    }

    #[Computed]
    public function expensesByCategory()
    {
        return ExpenseCategory::query()
            ->withSum('expenses', 'amount')
            ->withCount('expenses')
            ->orderByDesc('expenses_sum_amount')
            ->get();
    }

    #[Computed]
    public function netPosition(): float
    {
        return $this->totalRevenue - $this->totalExpenses;
    }

    public function render()
    {
        return view('livewire.reports.index');
    }
}
