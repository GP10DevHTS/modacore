<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
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

    public string $address = '';

    public string $idNumber = '';

    public string $notes = '';

    public ?int $editingId = null;

    public ?int $deletingId = null;

    #[Computed]
    public function customers()
    {
        return Customer::query()
            ->search($this->search)
            ->withCount('bookings')
            ->latest()
            ->paginate(15);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->js('$flux.modal("customer-form").show()');
    }

    public function openEdit(int $id): void
    {
        $this->resetForm();
        $customer = Customer::findOrFail($id);
        $this->editingId = $id;
        $this->name = $customer->name;
        $this->email = $customer->email ?? '';
        $this->phone = $customer->phone;
        $this->address = $customer->address ?? '';
        $this->idNumber = $customer->id_number ?? '';
        $this->notes = $customer->notes ?? '';
        $this->js('$flux.modal("customer-form").show()');
    }

    public function save(): void
    {
        abort_unless(auth()->user()->can($this->editingId ? 'customers.edit' : 'customers.create'), 403);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'idNumber' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ];

        if ($this->editingId) {
            $rules['email'][] = Rule::unique('customers', 'email')->ignore($this->editingId)->whereNull('deleted_at');
            $rules['idNumber'][] = Rule::unique('customers', 'id_number')->ignore($this->editingId)->whereNull('deleted_at');
        } else {
            $rules['email'][] = Rule::unique('customers', 'email')->whereNull('deleted_at');
            $rules['idNumber'][] = Rule::unique('customers', 'id_number')->whereNull('deleted_at');
        }

        $validated = $this->validate($rules);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'] ?: null,
            'phone' => $validated['phone'],
            'address' => $validated['address'] ?: null,
            'id_number' => $validated['idNumber'] ?: null,
            'notes' => $validated['notes'] ?: null,
        ];

        if ($this->editingId) {
            Customer::findOrFail($this->editingId)->update($data);
            Flux::toast('Customer updated successfully.');
        } else {
            Customer::create([...$data, 'created_by' => auth()->id()]);
            Flux::toast('Customer added successfully.');
        }

        unset($this->customers);
        $this->js('$flux.modal("customer-form").close()');
        $this->resetForm();
    }

    public function openDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->js('$flux.modal("confirm-delete-customer").show()');
    }

    public function delete(): void
    {
        abort_unless(auth()->user()->can('customers.delete'), 403);

        $customer = Customer::findOrFail($this->deletingId);

        if ($customer->activeBookings()->exists()) {
            Flux::toast(text: 'Cannot delete a customer with active or confirmed bookings.', variant: 'danger');
            $this->js('$flux.modal("confirm-delete-customer").close()');

            return;
        }

        $customer->delete();
        unset($this->customers);
        $this->js('$flux.modal("confirm-delete-customer").close()');
        Flux::toast('Customer deleted.');
        $this->deletingId = null;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->idNumber = '';
        $this->notes = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.customers.index');
    }
}
