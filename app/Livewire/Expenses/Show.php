<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public Expense $expense;

    public function mount(Expense $expense): void
    {
        abort_unless(auth()->user()->can('expenses.view'), 403);
        $this->expense = $expense;
    }

    #[Computed]
    public function bill(): Expense
    {
        return $this->expense->load([
            'item.category',
            'payments.createdBy',
            'createdBy',
        ]);
    }

    public function render()
    {
        return view('livewire.expenses.show');
    }
}
