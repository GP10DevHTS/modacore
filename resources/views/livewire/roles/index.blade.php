<div class="space-y-5">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Roles & Permissions</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Manage access roles and their permission sets.</p>
        </div>
        @can('employees.create')
        <button wire:click="openCreate"
            class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-black shadow-sm hover:bg-amber-400 active:bg-amber-600 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Role
        </button>
        @endcan
    </div>

    {{-- Roles Grid --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach($this->roles as $role)
            @php
                $isSuperAdmin = $role->name === 'superadmin';
                $isSystem = in_array($role->name, ['superadmin', 'manager', 'staff', 'viewer']);
                $border = match($role->name) {
                    'superadmin' => 'border-amber-200 dark:border-amber-800/40',
                    'manager'    => 'border-violet-200 dark:border-violet-800/40',
                    'staff'      => 'border-blue-200 dark:border-blue-800/40',
                    default      => 'border-zinc-200 dark:border-zinc-700/60',
                };
                $badge = match($role->name) {
                    'superadmin' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
                    'manager'    => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400',
                    'staff'      => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                    default      => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
                };
                $perms = $role->permissions->pluck('name');
            @endphp
            <div class="overflow-hidden rounded-xl border {{ $border }} bg-white shadow-sm dark:bg-zinc-900" wire:key="role-{{ $role->id }}">
                <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-100 dark:border-zinc-700/60">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold capitalize {{ $badge }}">{{ $role->name }}</span>
                        @if($isSuperAdmin)
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">All permissions (bypass)</span>
                        @else
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $perms->count() }} {{ Str::plural('permission', $perms->count()) }}</span>
                        @endif
                    </div>
                    @if(! $isSuperAdmin)
                        <div class="flex items-center gap-1">
                            @can('employees.edit')
                            <button wire:click="openEdit({{ $role->id }})"
                                class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors" title="Edit">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            @endcan
                            @if(! $isSystem)
                                @can('employees.delete')
                                <button wire:click="openDelete({{ $role->id }})"
                                    class="rounded-lg p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors" title="Delete">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @endcan
                            @endif
                        </div>
                    @endif
                </div>
                <div class="px-5 py-4">
                    @if($isSuperAdmin)
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">Bypasses all permission checks — always has full system access.</p>
                    @elseif($perms->isEmpty())
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">No permissions assigned.</p>
                    @else
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($perms->sort() as $perm)
                                <span class="rounded bg-zinc-100 px-2 py-0.5 font-mono text-xs text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                     @php
                                         [$module, $action] = explode('.', $perm);
                                         $label = ucfirst($action) . ' ' . ucfirst($module);
                                     @endphp
                                    {{ $label }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Role Form Modal --}}
    <flux:modal name="role-form" class="md:w-[42rem]">
        <form wire:submit="save" class="space-y-5">
            <div class="border-b border-zinc-100 pb-4 dark:border-zinc-700">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $editingId ? 'Edit Role' : 'New Role' }}</h3>
                <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Define a name and assign permissions.</p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Role Name <span class="text-red-500">*</span></label>
                <flux:input wire:model="name" placeholder="e.g. cashier" />
                <flux:error name="name" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Permissions</label>
                <div class="max-h-80 space-y-3 overflow-y-auto rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    @foreach($this->groupedPermissions as $group => $permissions)
                        <div>
                            <div class="mb-1.5 text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $group }}</div>
                            <div class="grid grid-cols-2 gap-1">
                                @foreach($permissions as $permission)
                                    <label class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                        <input type="checkbox"
                                            wire:model="selectedPermissions"
                                            value="{{ $permission->name }}"
                                            class="size-3.5 rounded border-zinc-300 text-amber-500 focus:ring-amber-400 dark:border-zinc-600">
                                        <span class="font-mono text-xs text-zinc-700 dark:text-zinc-300">
                                            @php
                                                [$module, $action] = explode('.', $permission->name);
                                                $label = ucfirst($action) . ' ' . ucfirst($module);
                                            @endphp
                                            {{ $label }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <flux:error name="selectedPermissions" />
            </div>

            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('role-form').close()" variant="ghost" type="button">Cancel</flux:button>
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-black hover:bg-amber-400 active:bg-amber-600 transition-colors">
                    {{ $editingId ? 'Save Changes' : 'Create Role' }}
                </button>
            </div>
        </form>
    </flux:modal>

    {{-- Confirm Delete Modal --}}
    <flux:modal name="confirm-delete-role" class="md:w-96">
        <div class="space-y-5">
            <div class="flex items-start gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="size-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Delete Role</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Users assigned this role will lose its permissions. This cannot be undone.</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('confirm-delete-role').close()" variant="ghost" type="button">Cancel</flux:button>
                <button wire:click="delete"
                    class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500 transition-colors">
                    Delete Role
                </button>
            </div>
        </div>
    </flux:modal>

</div>
