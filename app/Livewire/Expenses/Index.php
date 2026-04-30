<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';

    public string $categoryFilter = '';

    public string $statusFilter = '';

    // Expense form
    public ?int $editingId = null;

    public string $title = '';

    public ?int $categoryId = null;

    public string $amount = '';

    public string $expenseDate = '';

    public string $paymentMethod = 'cash';

    public string $reference = '';

    public string $status = 'draft';

    public string $notes = '';

    // Category form
    public ?int $editingCategoryId = null;

    public string $categoryName = '';

    public string $categoryDescription = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('expenses.view'), 403);
        $this->expenseDate = today()->toDateString();
    }

    #[Computed]
    public function expenses()
    {
        return Expense::query()
            ->with(['category', 'createdBy'])
            ->when($this->search, fn ($q) => $q
                ->where('title', 'like', "%{$this->search}%")
                ->orWhere('expense_number', 'like', "%{$this->search}%")
                ->orWhere('reference', 'like', "%{$this->search}%"))
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest('expense_date')
            ->latest('id')
            ->paginate(20);
    }

    #[Computed]
    public function categories()
    {
        return ExpenseCategory::orderBy('name')->get();
    }

    #[Computed]
    public function summaryApproved(): float
    {
        return (float) Expense::where('status', 'approved')->sum('amount');
    }

    #[Computed]
    public function summaryDraft(): float
    {
        return (float) Expense::where('status', 'draft')->sum('amount');
    }

    #[Computed]
    public function summaryThisMonth(): float
    {
        return (float) Expense::where('status', 'approved')
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');
    }

    // ─── Filter resets ───────────────────────────────────────────────────────

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    // ─── Expense CRUD ────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('expenses.create'), 403);
        $this->resetForm();
        $this->js('$flux.modal("expense-form").show()');
    }

    public function openEdit(int $id): void
    {
        abort_unless(auth()->user()->can('expenses.edit'), 403);
        $expense = Expense::findOrFail($id);
        $this->editingId = $id;
        $this->title = $expense->title;
        $this->categoryId = $expense->category_id;
        $this->amount = (string) $expense->amount;
        $this->expenseDate = $expense->expense_date->toDateString();
        $this->paymentMethod = $expense->payment_method;
        $this->reference = $expense->reference ?? '';
        $this->status = $expense->status;
        $this->notes = $expense->notes ?? '';
        $this->js('$flux.modal("expense-form").show()');
    }

    public function save(): void
    {
        $this->editingId
            ? abort_unless(auth()->user()->can('expenses.edit'), 403)
            : abort_unless(auth()->user()->can('expenses.create'), 403);

        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'categoryId' => ['nullable', 'exists:expense_categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expenseDate' => ['required', 'date'],
            'paymentMethod' => ['required', 'in:cash,card,mobile_money'],
            'reference' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,approved'],
            'notes' => ['nullable', 'string'],
        ]);

        $payload = [
            'title' => $this->title,
            'category_id' => $this->categoryId ?: null,
            'amount' => $this->amount,
            'expense_date' => $this->expenseDate,
            'payment_method' => $this->paymentMethod,
            'reference' => $this->reference ?: null,
            'status' => $this->status,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingId) {
            Expense::findOrFail($this->editingId)->update($payload);
            Flux::toast(text: 'Expense updated.', variant: 'success');
        } else {
            DB::transaction(function () use ($payload) {
                Expense::create(array_merge($payload, [
                    'expense_number' => Expense::nextNumber(),
                    'created_by' => auth()->id(),
                ]));
            });
            Flux::toast(text: 'Expense recorded.', variant: 'success');
        }

        $this->js('$flux.modal("expense-form").close()');
        $this->resetForm();
        unset($this->expenses, $this->summaryApproved, $this->summaryDraft, $this->summaryThisMonth);
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()->can('expenses.delete'), 403);
        Expense::findOrFail($id)->delete();
        Flux::toast(text: 'Expense deleted.', variant: 'success');
        unset($this->expenses, $this->summaryApproved, $this->summaryDraft, $this->summaryThisMonth);
    }

    // ─── Category CRUD ───────────────────────────────────────────────────────

    public function openCategories(): void
    {
        abort_unless(auth()->user()->can('expenses.create'), 403);
        $this->resetCategoryForm();
        $this->js('$flux.modal("expense-categories").show()');
    }

    public function saveCategory(): void
    {
        abort_unless(auth()->user()->can('expenses.create'), 403);

        $this->validate([
            'categoryName' => ['required', 'string', 'max:255'],
            'categoryDescription' => ['nullable', 'string'],
        ]);

        if ($this->editingCategoryId) {
            ExpenseCategory::findOrFail($this->editingCategoryId)->update([
                'name' => $this->categoryName,
                'description' => $this->categoryDescription ?: null,
            ]);
        } else {
            ExpenseCategory::create([
                'name' => $this->categoryName,
                'description' => $this->categoryDescription ?: null,
            ]);
        }

        $this->resetCategoryForm();
        unset($this->categories);
    }

    public function editCategory(int $id): void
    {
        abort_unless(auth()->user()->can('expenses.create'), 403);
        $cat = ExpenseCategory::findOrFail($id);
        $this->editingCategoryId = $id;
        $this->categoryName = $cat->name;
        $this->categoryDescription = $cat->description ?? '';
    }

    public function deleteCategory(int $id): void
    {
        abort_unless(auth()->user()->can('expenses.delete'), 403);
        $category = ExpenseCategory::withCount('expenses')->findOrFail($id);

        if ($category->expenses_count > 0) {
            Flux::toast(text: 'Cannot delete a category that has expenses.', variant: 'danger');

            return;
        }

        $category->delete();
        unset($this->categories);
    }

    public function cancelCategoryEdit(): void
    {
        $this->resetCategoryForm();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->categoryId = null;
        $this->amount = '';
        $this->expenseDate = today()->toDateString();
        $this->paymentMethod = 'cash';
        $this->reference = '';
        $this->status = 'draft';
        $this->notes = '';
        $this->resetValidation();
    }

    private function resetCategoryForm(): void
    {
        $this->editingCategoryId = null;
        $this->categoryName = '';
        $this->categoryDescription = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.expenses.index');
    }
}
