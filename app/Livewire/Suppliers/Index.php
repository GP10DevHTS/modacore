<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $contactPerson = '';

    public string $address = '';

    public string $notes = '';

    public ?int $editingId = null;

    public ?int $deletingId = null;

    #[Computed]
    public function suppliers()
    {
        return Supplier::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%");
            }))
            ->withCount('purchaseOrders')
            ->latest()
            ->paginate(15);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('inventory.create'), 403);
        $this->resetForm();
        $this->js('$flux.modal("supplier-form").show()');
    }

    public function openEdit(int $id): void
    {
        abort_unless(auth()->user()->can('inventory.edit'), 403);
        $this->resetForm();
        $supplier = Supplier::findOrFail($id);
        $this->editingId = $id;
        $this->name = $supplier->name;
        $this->email = $supplier->email ?? '';
        $this->phone = $supplier->phone ?? '';
        $this->contactPerson = $supplier->contact_person ?? '';
        $this->address = $supplier->address ?? '';
        $this->notes = $supplier->notes ?? '';
        $this->js('$flux.modal("supplier-form").show()');
    }

    public function save(): void
    {
        abort_unless(auth()->user()->can($this->editingId ? 'inventory.edit' : 'inventory.create'), 403);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')->ignore($this->editingId)->whereNull('deleted_at')],
            'phone' => ['nullable', 'string', 'max:50'],
            'contactPerson' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'] ?: null,
            'phone' => $validated['phone'] ?: null,
            'contact_person' => $validated['contactPerson'] ?: null,
            'address' => $validated['address'] ?: null,
            'notes' => $validated['notes'] ?: null,
        ];

        if ($this->editingId) {
            Supplier::findOrFail($this->editingId)->update($data);
            Flux::toast('Supplier updated.');
        } else {
            Supplier::create([...$data, 'created_by' => auth()->id()]);
            Flux::toast('Supplier added.');
        }

        unset($this->suppliers);
        $this->js('$flux.modal("supplier-form").close()');
        $this->resetForm();
    }

    public function openDelete(int $id): void
    {
        abort_unless(auth()->user()->can('inventory.delete'), 403);
        $this->deletingId = $id;
        $this->js('$flux.modal("confirm-delete-supplier").show()');
    }

    public function delete(): void
    {
        abort_unless(auth()->user()->can('inventory.delete'), 403);
        Supplier::findOrFail($this->deletingId)->delete();
        unset($this->suppliers);
        $this->js('$flux.modal("confirm-delete-supplier").close()');
        Flux::toast('Supplier deleted.');
        $this->deletingId = null;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->contactPerson = '';
        $this->address = '';
        $this->notes = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.suppliers.index');
    }
}
