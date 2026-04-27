<?php

namespace App\Livewire\Roles;

use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    public string $name = '';

    public array $selectedPermissions = [];

    public ?int $editingId = null;

    public ?int $deletingId = null;

    /** @var array<string, array<string>> */
    public array $permissionGroups = [];

    public function mount(): void
    {
        abort_unless(auth()->user()->can('employees.view'), 403);
    }

    #[Computed]
    public function roles()
    {
        return Role::with('permissions')->orderBy('name')->get();
    }

    #[Computed]
    public function allPermissions()
    {
        return Permission::orderBy('name')->get();
    }

    #[Computed]
    public function groupedPermissions(): array
    {
        $groups = [];

        foreach ($this->allPermissions as $permission) {
            [$group] = explode('.', $permission->name, 2) + ['', ''];
            $groups[$group][] = $permission;
        }

        ksort($groups);

        return $groups;
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('employees.create'), 403);

        $this->reset(['name', 'selectedPermissions', 'editingId']);
        $this->resetValidation();
        $this->js('$flux.modal("role-form").show()');
    }

    public function openEdit(int $id): void
    {
        abort_unless(auth()->user()->can('employees.edit'), 403);

        $role = Role::with('permissions')->findOrFail($id);

        if ($role->name === 'superadmin') {
            Flux::toast(text: 'The superadmin role cannot be edited.', variant: 'danger');

            return;
        }

        $this->editingId = $id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->resetValidation();
        $this->js('$flux.modal("role-form").show()');
    }

    public function save(): void
    {
        abort_unless(auth()->user()->can($this->editingId ? 'employees.edit' : 'employees.create'), 403);

        $rules = [
            'name' => [
                'required', 'string', 'max:50',
                Rule::unique('roles', 'name')->ignore($this->editingId)->where('guard_name', 'web'),
            ],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['string', Rule::exists('permissions', 'name')],
        ];

        $validated = $this->validate($rules);

        if ($this->editingId) {
            $role = Role::findOrFail($this->editingId);
            $role->update(['name' => $validated['name']]);
            $role->syncPermissions($validated['selectedPermissions']);
            Flux::toast('Role updated.');
        } else {
            $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
            $role->syncPermissions($validated['selectedPermissions']);
            Flux::toast('Role created.');
        }

        unset($this->roles);
        $this->js('$flux.modal("role-form").close()');
        $this->reset(['name', 'selectedPermissions', 'editingId']);
    }

    public function openDelete(int $id): void
    {
        abort_unless(auth()->user()->can('employees.delete'), 403);

        $role = Role::findOrFail($id);

        if (in_array($role->name, ['superadmin', 'manager', 'staff', 'viewer'])) {
            Flux::toast(text: 'System roles cannot be deleted.', variant: 'danger');

            return;
        }

        $this->deletingId = $id;
        $this->js('$flux.modal("confirm-delete-role").show()');
    }

    public function delete(): void
    {
        abort_unless(auth()->user()->can('employees.delete'), 403);

        Role::findOrFail($this->deletingId)->delete();
        unset($this->roles);
        $this->js('$flux.modal("confirm-delete-role").close()');
        Flux::toast('Role deleted.');
        $this->deletingId = null;
    }

    public function render()
    {
        return view('livewire.roles.index');
    }
}
