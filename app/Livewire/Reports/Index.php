<?php

namespace App\Livewire\Reports;

use App\Models\Booking;
use App\Models\Customer;
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

    public string $receiptDateFrom = '';

    public string $receiptDateTo = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('reports.view'), 403);
        $this->receiptDateFrom = now()->startOfMonth()->toDateString();
        $this->receiptDateTo = now()->toDateString();
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
    public function totalOutstandingCustomers(): float
    {
        $totalBooked = (float) Booking::whereNotIn('status', ['cancelled'])->sum('total_amount');
        $totalPaid = (float) Payment::sum('amount');

        return max(0, $totalBooked - $totalPaid);
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

    // ── Debtors ──────────────────────────────────────────────────────────────

    #[Computed]
    public function debtors()
    {
        return Customer::query()
            ->whereNull('deleted_at')
            ->whereHas('bookings', fn ($q) => $q->whereNotIn('status', ['cancelled']))
            ->with(['bookings' => fn ($q) => $q
                ->whereNotIn('status', ['cancelled'])
                ->withSum('payments', 'amount'),
            ])
            ->get()
            ->map(function (Customer $customer) {
                $totalBooked = (float) $customer->bookings->sum('total_amount');
                $totalPaid = (float) $customer->bookings->sum('payments_sum_amount');
                $outstanding = max(0, $totalBooked - $totalPaid);

                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'total_booked' => $totalBooked,
                    'total_paid' => $totalPaid,
                    'outstanding' => $outstanding,
                    'booking_count' => $customer->bookings->count(),
                ];
            })
            ->filter(fn (array $c) => $c['outstanding'] > 0)
            ->sortByDesc('outstanding')
            ->values();
    }

    // ── Receipts ─────────────────────────────────────────────────────────────

    public function updatedReceiptDateFrom(): void
    {
        unset($this->receiptsByPeriod, $this->receiptPeriodTotals);
    }

    public function updatedReceiptDateTo(): void
    {
        unset($this->receiptsByPeriod, $this->receiptPeriodTotals);
    }

    #[Computed]
    public function receiptsByPeriod()
    {
        return Payment::query()
            ->with(['booking.customer'])
            ->when($this->receiptDateFrom, fn ($q) => $q->whereDate('paid_at', '>=', $this->receiptDateFrom))
            ->when($this->receiptDateTo, fn ($q) => $q->whereDate('paid_at', '<=', $this->receiptDateTo))
            ->whereNotNull('paid_at')
            ->orderByDesc('paid_at')
            ->get();
    }

    #[Computed]
    public function receiptPeriodTotals(): array
    {
        $payments = $this->receiptsByPeriod;
        $totalOrdered = (float) Booking::whereNotIn('status', ['cancelled'])
            ->when($this->receiptDateFrom, fn ($q) => $q->whereDate('hire_from', '>=', $this->receiptDateFrom))
            ->when($this->receiptDateTo, fn ($q) => $q->whereDate('hire_from', '<=', $this->receiptDateTo))
            ->sum('total_amount');

        return [
            'total_collected' => (float) $payments->where('is_deposit', false)->sum('amount'),
            'total_deposits' => (float) $payments->where('is_deposit', true)->sum('amount'),
            'total_all' => (float) $payments->sum('amount'),
            'total_ordered' => $totalOrdered,
            'count' => $payments->count(),
        ];
    }

    public function render()
    {
        return view('livewire.reports.index');
    }
}
