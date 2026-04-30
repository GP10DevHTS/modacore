<?php

namespace App\Livewire\Expenses;

use App\Enums\PaymentStatus;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseItem;
use App\Models\ExpensePayment;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // ── Tab state ────────────────────────────────────────────────────────────
    public string $activeTab = 'bills';

    // ── Bills tab filters ────────────────────────────────────────────────────
    public string $search = '';

    public string $statusFilter = '';

    public string $categoryFilter = '';

    // ── Bill creation / editing form ─────────────────────────────────────────
    public ?int $editingId = null;

    public string $billTitle = '';

    public ?int $billItemId = null;

    public string $billAmount = '';

    public string $billDate = '';

    public string $billReference = '';

    public string $billNotes = '';

    // Optional initial payment
    public bool $hasInitialPayment = false;

    public string $payAmount = '';

    public string $payDate = '';

    public string $payMethod = 'cash';

    public string $payReference = '';

    // ── Items tab ────────────────────────────────────────────────────────────
    public string $searchItems = '';

    public ?int $selectedCategoryId = null;

    // ── Categories tab ───────────────────────────────────────────────────────
    public string $searchCategories = '';

    // ── Category form ────────────────────────────────────────────────────────
    public ?int $editingCategoryId = null;

    public string $categoryName = '';

    public string $categoryDescription = '';

    // ── Item form ────────────────────────────────────────────────────────────
    public ?int $editingItemId = null;

    public string $itemName = '';

    public string $itemDescription = '';

    public ?int $itemCategoryId = null;

    // ── Lifecycle ────────────────────────────────────────────────────────────

    public function mount(): void
    {
        abort_unless(auth()->user()->can('expenses.view'), 403);
        $this->billDate = today()->toDateString();
        $this->payDate = today()->toDateString();
    }

    // ── Tab navigation ───────────────────────────────────────────────────────

    public function setActiveTab(string $tab): void
    {
        abort_if(! in_array($tab, ['bills', 'items', 'categories']), 400);
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // ── Computed: Bills ──────────────────────────────────────────────────────

    #[Computed]
    public function bills()
    {
        return Expense::query()
            ->with(['item.category', 'payments'])
            ->when($this->search, fn ($q) => $q
                ->where('title', 'like', "%{$this->search}%")
                ->orWhere('expense_number', 'like', "%{$this->search}%")
                ->orWhere('reference', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('payment_status', $this->statusFilter))
            ->when($this->categoryFilter, fn ($q) => $q->whereHas('item', fn ($iq) => $iq->where('category_id', $this->categoryFilter)))
            ->latest('expense_date')
            ->latest('id')
            ->paginate(20);
    }

    #[Computed]
    public function summaryStats(): array
    {
        $totalBilled = (float) Expense::sum('amount');
        $totalPaid = (float) ExpensePayment::sum('amount');

        return [
            'total_billed' => $totalBilled,
            'total_paid' => $totalPaid,
            'balance' => max(0, $totalBilled - $totalPaid),
            'unpaid_count' => Expense::where('payment_status', PaymentStatus::Unpaid)->count(),
            'partial_count' => Expense::where('payment_status', PaymentStatus::PartiallyPaid)->count(),
            'paid_count' => Expense::where('payment_status', PaymentStatus::FullyPaid)->count(),
        ];
    }

    // ── Computed: Categories ─────────────────────────────────────────────────

    #[Computed]
    public function allCategories()
    {
        return ExpenseCategory::withCount('items')->orderBy('name')->get();
    }

    #[Computed]
    public function categories()
    {
        return ExpenseCategory::query()
            ->withCount('items')
            ->when($this->searchCategories, fn ($q) => $q->where('name', 'like', "%{$this->searchCategories}%"))
            ->orderBy('name')
            ->get();
    }

    // ── Computed: Items ──────────────────────────────────────────────────────

    #[Computed]
    public function items()
    {
        return ExpenseItem::query()
            ->with('category')
            ->withCount('expenses')
            ->when($this->selectedCategoryId, fn ($q) => $q->where('category_id', $this->selectedCategoryId))
            ->when($this->searchItems, fn ($q) => $q->where('name', 'like', "%{$this->searchItems}%"))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function allItems()
    {
        return ExpenseItem::query()
            ->with('category')
            ->get()
            ->sortBy(fn ($item) => [$item->category?->name ?? '', $item->name])
            ->values()
            ->map(fn ($item) => (object) [
                'id' => $item->id,
                'name' => ($item->category?->name ? $item->category->name.' → ' : '').$item->name,
            ]);
    }

    // ── Filter resets ────────────────────────────────────────────────────────

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    // ── Bill CRUD ────────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('expenses.create'), 403);
        $this->resetBillForm();
        $this->js('$flux.modal("bill-form").show()');
    }

    public function openEdit(int $id): void
    {
        abort_unless(auth()->user()->can('expenses.edit'), 403);
        $bill = Expense::findOrFail($id);
        $this->editingId = $id;
        $this->billTitle = $bill->title;
        $this->billItemId = $bill->item_id;
        $this->billAmount = (string) $bill->amount;
        $this->billDate = $bill->expense_date->toDateString();
        $this->billReference = $bill->reference ?? '';
        $this->billNotes = $bill->notes ?? '';
        $this->hasInitialPayment = false;
        $this->js('$flux.modal("bill-form").show()');
    }

    public function saveBill(): void
    {
        $this->editingId
            ? abort_unless(auth()->user()->can('expenses.edit'), 403)
            : abort_unless(auth()->user()->can('expenses.create'), 403);

        $rules = [
            'billTitle' => ['required', 'string', 'max:255'],
            'billItemId' => ['required', 'exists:expense_items,id'],
            'billAmount' => ['required', 'numeric', 'min:0.01'],
            'billDate' => ['required', 'date'],
            'billReference' => ['nullable', 'string', 'max:255'],
            'billNotes' => ['nullable', 'string'],
        ];

        if ($this->hasInitialPayment) {
            $rules['payAmount'] = ['required', 'numeric', 'min:0.01'];
            $rules['payDate'] = ['required', 'date'];
            $rules['payMethod'] = ['required', 'in:cash,card,mobile_money'];
        }

        $this->validate($rules);

        $payload = [
            'title' => $this->billTitle,
            'item_id' => $this->billItemId,
            'amount' => $this->billAmount,
            'expense_date' => $this->billDate,
            'reference' => $this->billReference ?: null,
            'notes' => $this->billNotes ?: null,
        ];

        DB::transaction(function () use ($payload) {
            if ($this->editingId) {
                $bill = Expense::findOrFail($this->editingId);
                $bill->update($payload);
                $bill->recomputePaymentStatus();
                Flux::toast(text: 'Bill updated.', variant: 'success');
            } else {
                $bill = Expense::create(array_merge($payload, [
                    'expense_number' => Expense::nextNumber(),
                    'payment_status' => PaymentStatus::Unpaid,
                    'created_by' => auth()->id(),
                ]));

                if ($this->hasInitialPayment) {
                    ExpensePayment::create([
                        'expense_id' => $bill->id,
                        'amount' => $this->payAmount,
                        'payment_date' => $this->payDate,
                        'payment_method' => $this->payMethod,
                        'reference' => $this->payReference ?: null,
                        'created_by' => auth()->id(),
                    ]);
                    $bill->recomputePaymentStatus();
                }

                Flux::toast(text: 'Bill recorded.', variant: 'success');
            }
        });

        $this->js('$flux.modal("bill-form").close()');
        $this->resetBillForm();
        unset($this->bills, $this->summaryStats);
    }

    public function deleteBill(int $id): void
    {
        abort_unless(auth()->user()->can('expenses.delete'), 403);
        Expense::findOrFail($id)->delete();
        Flux::toast(text: 'Bill deleted.', variant: 'success');
        unset($this->bills, $this->summaryStats);
    }

    // ── Category CRUD ────────────────────────────────────────────────────────

    public function openCreateCategory(): void
    {
        abort_unless(auth()->user()->can('expenses.create'), 403);
        $this->resetCategoryForm();
        $this->js('$flux.modal("category-form").show()');
    }

    public function openEditCategory(int $id): void
    {
        abort_unless(auth()->user()->can('expenses.create'), 403);
        $cat = ExpenseCategory::findOrFail($id);
        $this->editingCategoryId = $id;
        $this->categoryName = $cat->name;
        $this->categoryDescription = $cat->description ?? '';
        $this->js('$flux.modal("category-form").show()');
    }

    public function saveCategory(): void
    {
        abort_unless(auth()->user()->can('expenses.create'), 403);

        $isEditing = (bool) $this->editingCategoryId;

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

        $this->js('$flux.modal("category-form").close()');
        $this->resetCategoryForm();
        unset($this->categories, $this->allCategories, $this->allItems);
        Flux::toast(text: $isEditing ? 'Category updated.' : 'Category added.', variant: 'success');
    }

    public function deleteCategory(int $id): void
    {
        abort_unless(auth()->user()->can('expenses.delete'), 403);
        $category = ExpenseCategory::withCount('items')->findOrFail($id);

        if ($category->items_count > 0) {
            Flux::toast(text: 'Cannot delete a category that has items. Remove its items first.', variant: 'danger');

            return;
        }

        $category->delete();
        unset($this->categories, $this->allCategories, $this->items, $this->allItems);
        Flux::toast(text: 'Category deleted.', variant: 'success');
    }

    // ── Item CRUD ────────────────────────────────────────────────────────────

    public function openCreateItem(): void
    {
        abort_unless(auth()->user()->can('expenses.create'), 403);
        $this->resetItemForm();
        $this->js('$flux.modal("item-form").show()');
    }

    public function openEditItem(int $id): void
    {
        abort_unless(auth()->user()->can('expenses.create'), 403);
        $item = ExpenseItem::findOrFail($id);
        $this->editingItemId = $id;
        $this->itemName = $item->name;
        $this->itemDescription = $item->description ?? '';
        $this->itemCategoryId = $item->category_id;
        $this->js('$flux.modal("item-form").show()');
    }

    public function saveItem(): void
    {
        abort_unless(auth()->user()->can('expenses.create'), 403);

        $isEditing = (bool) $this->editingItemId;

        $this->validate([
            'itemName' => ['required', 'string', 'max:255'],
            'itemDescription' => ['nullable', 'string'],
            'itemCategoryId' => ['required', 'exists:expense_categories,id'],
        ]);

        if ($this->editingItemId) {
            ExpenseItem::findOrFail($this->editingItemId)->update([
                'name' => $this->itemName,
                'description' => $this->itemDescription ?: null,
                'category_id' => $this->itemCategoryId,
            ]);
        } else {
            ExpenseItem::create([
                'name' => $this->itemName,
                'description' => $this->itemDescription ?: null,
                'category_id' => $this->itemCategoryId,
            ]);
        }

        $this->js('$flux.modal("item-form").close()');
        $this->resetItemForm();
        unset($this->items, $this->allCategories, $this->categories, $this->allItems);
        Flux::toast(text: $isEditing ? 'Item updated.' : 'Item added.', variant: 'success');
    }

    public function deleteItem(int $id): void
    {
        abort_unless(auth()->user()->can('expenses.delete'), 403);
        $item = ExpenseItem::withCount('expenses')->findOrFail($id);

        if ($item->expenses_count > 0) {
            Flux::toast(text: 'Cannot delete an item that has bills recorded against it.', variant: 'danger');

            return;
        }

        $item->delete();
        unset($this->items, $this->categories, $this->allCategories, $this->allItems);
        Flux::toast(text: 'Item deleted.', variant: 'success');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function resetBillForm(): void
    {
        $this->editingId = null;
        $this->billTitle = '';
        $this->billItemId = null;
        $this->billAmount = '';
        $this->billDate = today()->toDateString();
        $this->billReference = '';
        $this->billNotes = '';
        $this->hasInitialPayment = false;
        $this->payAmount = '';
        $this->payDate = today()->toDateString();
        $this->payMethod = 'cash';
        $this->payReference = '';
        $this->resetValidation();
    }

    private function resetCategoryForm(): void
    {
        $this->editingCategoryId = null;
        $this->categoryName = '';
        $this->categoryDescription = '';
        $this->resetValidation();
    }

    private function resetItemForm(): void
    {
        $this->editingItemId = null;
        $this->itemName = '';
        $this->itemDescription = '';
        $this->itemCategoryId = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.expenses.index');
    }
}
