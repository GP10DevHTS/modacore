<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use App\Models\ExpensePayment;
use Flux\Flux;
use Livewire\Component;

class PaymentForm extends Component
{
    public Expense $expense;

    public string $amount = '';

    public string $paymentDate = '';

    public string $paymentMethod = 'cash';

    public string $reference = '';

    public string $notes = '';

    public function mount(Expense $expense): void
    {
        abort_unless(auth()->user()->can('expenses.create'), 403);
        $this->expense = $expense->load('payments');
        $this->paymentDate = today()->toDateString();
        $this->amount = (string) max(0, $expense->balance());
    }

    public function save(): void
    {
        abort_unless(auth()->user()->can('expenses.create'), 403);

        $this->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paymentDate' => ['required', 'date'],
            'paymentMethod' => ['required', 'in:cash,card,mobile_money'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        ExpensePayment::create([
            'expense_id' => $this->expense->id,
            'amount' => $this->amount,
            'payment_date' => $this->paymentDate,
            'payment_method' => $this->paymentMethod,
            'reference' => $this->reference ?: null,
            'notes' => $this->notes ?: null,
            'created_by' => auth()->id(),
        ]);

        $this->expense->recomputePaymentStatus();

        Flux::toast(text: 'Payment recorded successfully.', variant: 'success');
        $this->redirectRoute('expenses.show', $this->expense->id, navigate: true);
    }

    public function render()
    {
        return view('livewire.expenses.payment-form');
    }
}
