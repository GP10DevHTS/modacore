<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Inventory')]
class Index extends Component
{
    use WithPagination;

    public string $activeTab = 'items';

    // ── Item search ──────────────────────────────────────────────
    public string $search = '';

    // ── Item form ────────────────────────────────────────────────
    public string $name = '';

    public string $description = '';

    public ?int $categoryId = null;

    public string $sku = '';

    public string $baseRentalPrice = '';

    public int $stockQuantity = 1;

    public bool $isActive = true;

    public ?int $editingId = null;

    public ?int $deletingId = null;

    // ── Category form ────────────────────────────────────────────
    public string $catName = '';

    public string $catDescription = '';

    public ?int $editingCategoryId = null;

    public ?int $deletingCategoryId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // ── Computed ─────────────────────────────────────────────────

    #[Computed]
    public function items()
    {
        return InventoryItem::with('category')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('sku', 'like', "%{$this->search}%");
            }))
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function categories()
    {
        return InventoryCategory::withCount('items')->ordered()->get();
    }

    // ── Item CRUD ─────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetItemForm();
        $this->editingId = null;
        $this->js('$flux.modal("item-form").show()');
    }

    public function openEdit(int $id): void
    {
        $item = InventoryItem::findOrFail($id);

        $this->editingId = $item->id;
        $this->name = $item->name;
        $this->description = $item->description ?? '';
        $this->categoryId = $item->category_id;
        $this->sku = $item->sku ?? '';
        $this->baseRentalPrice = (string) $item->base_rental_price;
        $this->stockQuantity = $item->stock_quantity;
        $this->isActive = $item->is_active;

        $this->js('$flux.modal("item-form").show()');
    }

    public function saveItem(): void
    {
        abort_unless(auth()->user()->can($this->editingId ? 'inventory.edit' : 'inventory.create'), 403);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'categoryId' => ['required', Rule::exists('inventory_categories', 'id')],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('inventory_items', 'sku')->ignore($this->editingId)],
            'baseRentalPrice' => ['required', 'numeric', 'min:0'],
            'stockQuantity' => ['required', 'integer', 'min:0'],
            'isActive' => ['boolean'],
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category_id' => $validated['categoryId'],
            'sku' => $validated['sku'] ?: null,
            'base_rental_price' => $validated['baseRentalPrice'],
            'stock_quantity' => $validated['stockQuantity'],
            'is_active' => $validated['isActive'],
        ];

        if ($this->editingId) {
            InventoryItem::findOrFail($this->editingId)->update($data);
            Flux::toast('Item updated.');
        } else {
            InventoryItem::create($data);
            Flux::toast('Item created.');
        }

        $this->js('$flux.modal("item-form").close()');
        $this->resetItemForm();
        unset($this->items);
    }

    public function openDeleteItem(int $id): void
    {
        $this->deletingId = $id;
        $this->js('$flux.modal("confirm-delete-item").show()');
    }

    public function deleteItem(): void
    {
        abort_unless(auth()->user()->can('inventory.delete'), 403);

        InventoryItem::findOrFail($this->deletingId)->delete();

        Flux::toast('Item deleted.');

        $this->deletingId = null;
        $this->js('$flux.modal("confirm-delete-item").close()');
        unset($this->items);
    }

    public function toggleActive(int $id): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        $item = InventoryItem::findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);
        unset($this->items);
    }

    // ── Category CRUD ─────────────────────────────────────────────

    public function openCreateCategory(): void
    {
        $this->resetCategoryForm();
        $this->editingCategoryId = null;
        $this->js('$flux.modal("category-form").show()');
    }

    public function openEditCategory(int $id): void
    {
        $category = InventoryCategory::findOrFail($id);

        $this->editingCategoryId = $category->id;
        $this->catName = $category->name;
        $this->catDescription = $category->description ?? '';

        $this->js('$flux.modal("category-form").show()');
    }

    public function saveCategory(): void
    {
        abort_unless(auth()->user()->can($this->editingCategoryId ? 'inventory.edit' : 'inventory.create'), 403);

        $validated = $this->validate([
            'catName' => ['required', 'string', 'max:255', Rule::unique('inventory_categories', 'name')->ignore($this->editingCategoryId)->whereNull('deleted_at')],
            'catDescription' => ['nullable', 'string'],
        ]);

        $data = [
            'name' => $validated['catName'],
            'description' => $validated['catDescription'],
            'user_id' => auth()->id(),
        ];

        if ($this->editingCategoryId) {
            InventoryCategory::findOrFail($this->editingCategoryId)->update($data);
            Flux::toast('Category updated.');
        } else {
            InventoryCategory::create($data);
            Flux::toast('Category created.');
        }

        $this->js('$flux.modal("category-form").close()');
        $this->resetCategoryForm();
        unset($this->categories);
    }

    public function openDeleteCategory(int $id): void
    {
        $this->deletingCategoryId = $id;
        $this->js('$flux.modal("confirm-delete-category").show()');
    }

    public function deleteCategory(): void
    {
        abort_unless(auth()->user()->can('inventory.delete'), 403);

        $category = InventoryCategory::withCount('items')->findOrFail($this->deletingCategoryId);

        if ($category->items_count > 0) {
            Flux::toast(text: 'Cannot delete a category with existing items.', variant: 'danger');
            $this->js('$flux.modal("confirm-delete-category").close()');

            return;
        }

        $category->delete();

        Flux::toast('Category deleted.');

        $this->deletingCategoryId = null;
        $this->js('$flux.modal("confirm-delete-category").close()');
        unset($this->categories);
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function resetItemForm(): void
    {
        $this->name = '';
        $this->description = '';
        $this->categoryId = null;
        $this->sku = '';
        $this->baseRentalPrice = '';
        $this->stockQuantity = 1;
        $this->isActive = true;
        $this->resetValidation(['name', 'description', 'categoryId', 'sku', 'baseRentalPrice', 'stockQuantity', 'isActive']);
    }

    private function resetCategoryForm(): void
    {
        $this->catName = '';
        $this->catDescription = '';
        $this->resetValidation(['catName', 'catDescription']);
    }

    public function render()
    {
        return view('livewire.inventory.index');
    }
}
