<?php

namespace App\Livewire\Dashboard;

use App\Enums\PaymentStatus;
use App\Models\Expense;
use App\Models\ExpensePayment;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ExpensesSummary extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()->can('expenses.view'), 403);
    }

    #[Computed]
    public function totalBilled(): float
    {
        return (float) Expense::sum('amount');
    }

    #[Computed]
    public function totalPaid(): float
    {
        return (float) ExpensePayment::sum('amount');
    }

    #[Computed]
    public function outstanding(): float
    {
        return max(0, $this->totalBilled - $this->totalPaid);
    }

    #[Computed]
    public function thisMonthBilled(): float
    {
        return (float) Expense::whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');
    }

    #[Computed]
    public function unpaidCount(): int
    {
        return Expense::where('payment_status', PaymentStatus::Unpaid)->count()
            + Expense::where('payment_status', PaymentStatus::PartiallyPaid)->count();
    }

    public function render()
    {
        return view('livewire.dashboard.expenses-summary');
    }
}
