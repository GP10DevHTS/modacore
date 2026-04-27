<?php

namespace App\Livewire\Employees;

use App\Models\User;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $jobTitle = '';

    public string $role = '';

    public string $password = '';

    public bool $isActive = true;

    public ?int $editingId = null;

    public ?int $deletingId = null;

    #[Computed]
    public function employees()
    {
        return User::query()
            ->with('roles')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('job_title', 'like', "%{$this->search}%");
            }))
            ->orderBy('name')
            ->paginate(15);
    }

    #[Computed]
    public function availableRoles()
    {
        return Role::whereNot('name', 'superadmin')->orderBy('name')->get();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->js('$flux.modal("employee-form").show()');
    }

    public function openEdit(int $id): void
    {
        $this->resetForm();
        $user = User::with('roles')->findOrFail($id);

        if ($user->hasRole('superadmin')) {
            Flux::toast(text: 'The superadmin account cannot be edited.', variant: 'danger');

            return;
        }

        $this->editingId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->jobTitle = $user->job_title ?? '';
        $this->role = $user->roles->first()?->name ?? '';
        $this->isActive = $user->is_active;
        $this->js('$flux.modal("employee-form").show()');
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingId)],
            'phone' => ['nullable', 'string', 'max:50'],
            'jobTitle' => ['nullable', 'string', 'max:100'],
            'role' => ['required', Rule::in(Role::whereNot('name', 'superadmin')->pluck('name'))],
            'isActive' => ['boolean'],
        ];

        if (! $this->editingId) {
            $rules['password'] = ['required', 'string', 'min:8'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8'];
        }

        $validated = $this->validate($rules);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?: null,
            'job_title' => $validated['jobTitle'] ?: null,
            'is_active' => $validated['isActive'],
        ];

        if ($validated['password']) {
            $data['password'] = $validated['password'];
        }

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $user->update($data);
            $user->syncRoles([$validated['role']]);
            Flux::toast('Employee updated.');
        } else {
            $user = User::create($data);
            $user->assignRole($validated['role']);
            Flux::toast('Employee created.');
        }

        unset($this->employees);
        $this->js('$flux.modal("employee-form").close()');
        $this->resetForm();
    }

    public function openDelete(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('superadmin')) {
            Flux::toast(text: 'The superadmin account cannot be deleted.', variant: 'danger');

            return;
        }

        if ($user->id === auth()->id()) {
            Flux::toast(text: 'You cannot delete your own account.', variant: 'danger');

            return;
        }

        $this->deletingId = $id;
        $this->js('$flux.modal("confirm-delete-employee").show()');
    }

    public function delete(): void
    {
        $user = User::findOrFail($this->deletingId);
        $user->delete();
        unset($this->employees);
        $this->js('$flux.modal("confirm-delete-employee").close()');
        Flux::toast('Employee removed.');
        $this->deletingId = null;
    }

    public function toggleActive(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('superadmin')) {
            Flux::toast(text: 'The superadmin account cannot be deactivated.', variant: 'danger');

            return;
        }

        $user->update(['is_active' => ! $user->is_active]);
        unset($this->employees);
        Flux::toast($user->is_active ? 'Employee activated.' : 'Employee deactivated.');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->jobTitle = '';
        $this->role = '';
        $this->password = '';
        $this->isActive = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.employees.index');
    }
}
