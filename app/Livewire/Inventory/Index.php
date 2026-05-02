<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\VariantType;
use App\Models\VariantTypeValue;
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

    public string $costPrice = '';

    public int $stockQuantity = 1;

    public int $availableQuantity = 1;

    public bool $isActive = true;

    public ?int $editingId = null;

    public ?int $deletingId = null;

    // ── Category form ────────────────────────────────────────────
    public string $catName = '';

    public string $catDescription = '';

    public ?int $editingCategoryId = null;

    public ?int $deletingCategoryId = null;

    // ── Variant Type form ─────────────────────────────────────────
    public string $vtName = '';

    public ?int $editingVariantTypeId = null;

    public ?int $deletingVariantTypeId = null;

    // ── Variant Type Value form ────────────────────────────────────
    public string $vtvLabel = '';

    public ?int $editingValueId = null;

    public ?int $deletingValueId = null;

    public ?int $managingValuesForTypeId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->managingValuesForTypeId = null;
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

    #[Computed]
    public function variantTypes()
    {
        return VariantType::withCount('values')->ordered()->get();
    }

    #[Computed]
    public function managingVariantType(): ?VariantType
    {
        if (! $this->managingValuesForTypeId) {
            return null;
        }

        return VariantType::with(['values' => fn ($q) => $q->ordered()])->find($this->managingValuesForTypeId);
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
        $this->costPrice = $item->cost_price !== null ? (string) $item->cost_price : '';
        $this->stockQuantity = $item->stock_quantity;
        $this->availableQuantity = $item->available_quantity;
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
            'costPrice' => ['nullable', 'numeric', 'min:0'],
            'stockQuantity' => ['required', 'integer', 'min:0'],
            'availableQuantity' => ['required', 'integer', 'min:0', 'lte:stockQuantity'],
            'isActive' => ['boolean'],
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category_id' => $validated['categoryId'],
            'sku' => $validated['sku'] ?: null,
            'base_rental_price' => $validated['baseRentalPrice'],
            'cost_price' => $validated['costPrice'] ?: null,
//            'stock_quantity' => $validated['stockQuantity'],
//            'available_quantity' => $validated['availableQuantity'],
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

    // ── Variant Types CRUD ────────────────────────────────────────

    public function openCreateVariantType(): void
    {
        $this->vtName = '';
        $this->editingVariantTypeId = null;
        $this->resetValidation(['vtName']);
        $this->js('$flux.modal("variant-type-form").show()');
    }

    public function openEditVariantType(int $id): void
    {
        $vt = VariantType::findOrFail($id);
        $this->editingVariantTypeId = $vt->id;
        $this->vtName = $vt->name;
        $this->resetValidation(['vtName']);
        $this->js('$flux.modal("variant-type-form").show()');
    }

    public function saveVariantType(): void
    {
        abort_unless(auth()->user()->can($this->editingVariantTypeId ? 'inventory.edit' : 'inventory.create'), 403);

        $validated = $this->validate([
            'vtName' => ['required', 'string', 'max:100', Rule::unique('variant_types', 'name')->ignore($this->editingVariantTypeId)],
        ]);

        if ($this->editingVariantTypeId) {
            VariantType::findOrFail($this->editingVariantTypeId)->update(['name' => $validated['vtName']]);
            Flux::toast('Variant type updated.');
        } else {
            VariantType::create(['name' => $validated['vtName'], 'sort_order' => VariantType::max('sort_order') + 1]);
            Flux::toast('Variant type created.');
        }

        $this->vtName = '';
        $this->editingVariantTypeId = null;
        $this->js('$flux.modal("variant-type-form").close()');
        unset($this->variantTypes);
    }

    public function openDeleteVariantType(int $id): void
    {
        $this->deletingVariantTypeId = $id;
        $this->js('$flux.modal("confirm-delete-variant-type").show()');
    }

    public function deleteVariantType(): void
    {
        abort_unless(auth()->user()->can('inventory.delete'), 403);

        VariantType::findOrFail($this->deletingVariantTypeId)->delete();

        Flux::toast('Variant type deleted.');
        $this->deletingVariantTypeId = null;
        $this->js('$flux.modal("confirm-delete-variant-type").close()');
        unset($this->variantTypes, $this->managingVariantType);
    }

    // ── Variant Type Values CRUD ──────────────────────────────────

    public function openManageValues(int $typeId): void
    {
        $this->managingValuesForTypeId = $typeId;
        $this->vtvLabel = '';
        $this->editingValueId = null;
        $this->resetValidation(['vtvLabel']);
        unset($this->managingVariantType);
        $this->js('$flux.modal("variant-values-modal").show()');
    }

    public function saveVariantTypeValue(): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);

        $validated = $this->validate([
            'vtvLabel' => ['required', 'string', 'max:100'],
        ]);

        if ($this->editingValueId) {
            VariantTypeValue::findOrFail($this->editingValueId)->update(['label' => $validated['vtvLabel']]);
        } else {
            VariantTypeValue::create([
                'variant_type_id' => $this->managingValuesForTypeId,
                'label' => $validated['vtvLabel'],
                'sort_order' => VariantTypeValue::where('variant_type_id', $this->managingValuesForTypeId)->max('sort_order') + 1,
            ]);
        }

        $this->vtvLabel = '';
        $this->editingValueId = null;
        $this->resetValidation(['vtvLabel']);
        unset($this->managingVariantType, $this->variantTypes);
    }

    public function editVariantTypeValue(int $id): void
    {
        $value = VariantTypeValue::findOrFail($id);
        $this->editingValueId = $value->id;
        $this->vtvLabel = $value->label;
    }

    public function cancelEditValue(): void
    {
        $this->editingValueId = null;
        $this->vtvLabel = '';
        $this->resetValidation(['vtvLabel']);
    }

    public function deleteVariantTypeValue(int $id): void
    {
        abort_unless(auth()->user()->can('inventory.delete'), 403);

        VariantTypeValue::findOrFail($id)->delete();
        Flux::toast('Value deleted.');
        unset($this->managingVariantType, $this->variantTypes);
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function resetItemForm(): void
    {
        $this->name = '';
        $this->description = '';
        $this->categoryId = null;
        $this->sku = '';
        $this->baseRentalPrice = '';
        $this->costPrice = '';
        $this->stockQuantity = 1;
        $this->availableQuantity = 1;
        $this->isActive = true;
        $this->resetValidation(['name', 'description', 'categoryId', 'sku', 'baseRentalPrice', 'costPrice', 'stockQuantity', 'availableQuantity', 'isActive']);
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
