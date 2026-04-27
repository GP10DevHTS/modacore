<div class="space-y-5">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Employees</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Manage staff accounts and access roles.</p>
        </div>
        @can('employees.create')
        <button wire:click="openCreate"
            class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-black shadow-sm hover:bg-amber-400 active:bg-amber-600 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add Employee
        </button>
        @endcan
    </div>

    {{-- Table Card --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">

        {{-- Toolbar --}}
        <div class="flex items-center justify-between gap-4 border-b border-zinc-200 bg-zinc-50/60 px-5 py-3 dark:border-zinc-700/60 dark:bg-zinc-800/40">
            <div class="relative w-72">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search name, email or title…"
                    class="w-full rounded-lg border border-zinc-200 bg-white py-2 pl-9 pr-3 text-sm text-zinc-900 placeholder-zinc-400 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder-zinc-500"
                />
            </div>
            <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500">
                {{ $this->employees->total() }} {{ Str::plural('employee', $this->employees->total()) }}
            </span>
        </div>

        {{-- Table --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Employee</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Contact</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Role</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @forelse ($this->employees as $employee)
                    @php $isSuperAdmin = $employee->hasRole('superadmin'); @endphp
                    <tr class="group transition-colors hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30" wire:key="employee-{{ $employee->id }}">

                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="flex size-8 shrink-0 items-center justify-center rounded-full text-xs font-bold
                                    {{ $isSuperAdmin ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">
                                    {{ $employee->initials() }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $employee->name }}</span>
                                        @if($isSuperAdmin)
                                            <span class="rounded bg-amber-100 px-1.5 py-0.5 text-xs font-bold text-amber-700 dark:bg-amber-900/40 dark:text-amber-400">SUPER</span>
                                        @endif
                                    </div>
                                    @if($employee->job_title)
                                        <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">{{ $employee->job_title }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="px-5 py-3.5">
                            <div class="text-zinc-800 dark:text-zinc-200">{{ $employee->email }}</div>
                            @if($employee->phone)
                                <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">{{ $employee->phone }}</div>
                            @endif
                        </td>

                        <td class="px-5 py-3.5 text-center">
                            @php
                                $roleName = $employee->roles->first()?->name ?? '—';
                                $roleColors = [
                                    'superadmin' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    'manager'    => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400',
                                    'staff'      => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'viewer'     => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
                                ];
                                $roleColor = $roleColors[$roleName] ?? 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400';
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $roleColor }}">
                                {{ $roleName }}
                            </span>
                        </td>

                        <td class="px-5 py-3.5 text-center">
                            @if($isSuperAdmin)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400">
                                    <span class="size-1.5 rounded-full bg-emerald-500"></span>Active
                                </span>
                            @else
                                <button wire:click="toggleActive({{ $employee->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold transition-colors
                                        {{ $employee->is_active
                                            ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400'
                                            : 'bg-zinc-100 text-zinc-500 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                    <span class="size-1.5 rounded-full {{ $employee->is_active ? 'bg-emerald-500' : 'bg-zinc-400' }}"></span>
                                    {{ $employee->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            @endif
                        </td>

                        <td class="px-5 py-3.5 text-right">
                            @if(! $isSuperAdmin)
                                <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                    @can('employees.edit')
                                    <button wire:click="resendSetupLink({{ $employee->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-amber-900/20 dark:hover:text-amber-400 transition-colors"
                                        title="Resend password setup link">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="openEdit({{ $employee->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors"
                                        title="Edit">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    @endcan
                                    @can('employees.delete')
                                    <button wire:click="openDelete({{ $employee->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors"
                                        title="Remove">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endcan
                                </div>
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                @if($search) No employees match "{{ $search }}" @else No employees yet @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($this->employees->hasPages())
            <div class="border-t border-zinc-200 px-5 py-3 dark:border-zinc-700/60">
                {{ $this->employees->links() }}
            </div>
        @endif

    </div>

    {{-- Employee Form Modal --}}
    <flux:modal name="employee-form" class="md:w-[36rem]">
        <form wire:submit="save" class="space-y-5">
            <div class="border-b border-zinc-100 pb-4 dark:border-zinc-700">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $editingId ? 'Edit Employee' : 'Add Employee' }}
                </h3>
                <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $editingId ? 'Update employee details and role.' : 'A password setup link will be emailed to the new employee.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Full Name <span class="text-red-500">*</span></label>
                    <flux:input wire:model="name" placeholder="e.g. John Smith" />
                    <flux:error name="name" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Email <span class="text-red-500">*</span></label>
                    <flux:input wire:model="email" type="email" placeholder="john@example.com" />
                    <flux:error name="email" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Phone</label>
                    <flux:input wire:model="phone" placeholder="+256 700 000 000" />
                    <flux:error name="phone" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Job Title</label>
                    <flux:input wire:model="jobTitle" placeholder="e.g. Store Manager" />
                    <flux:error name="jobTitle" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Role <span class="text-red-500">*</span></label>
                    <flux:select wire:model="role" placeholder="Select a role">
                        @foreach($this->availableRoles as $r)
                            <flux:select.option value="{{ $r->name }}">{{ ucfirst($r->name) }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="role" />
                </div>
                @if($editingId)
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Status</label>
                        <label class="flex cursor-pointer items-center gap-2.5">
                            <flux:checkbox wire:model="isActive" />
                            <span class="text-sm text-zinc-700 dark:text-zinc-300">Active account</span>
                        </label>
                    </div>
                @endif
            </div>

            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('employee-form').close()" variant="ghost" type="button">Cancel</flux:button>
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-black hover:bg-amber-400 active:bg-amber-600 transition-colors">
                    {{ $editingId ? 'Save Changes' : 'Create Employee' }}
                </button>
            </div>
        </form>
    </flux:modal>

    {{-- Confirm Delete Modal --}}
    <flux:modal name="confirm-delete-employee" class="md:w-96">
        <div class="space-y-5">
            <div class="flex items-start gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="size-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Remove Employee</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        This will permanently remove the employee's account. This action cannot be undone.
                    </p>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('confirm-delete-employee').close()" variant="ghost" type="button">Cancel</flux:button>
                <button wire:click="delete"
                    class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500 transition-colors">
                    Remove Employee
                </button>
            </div>
        </div>
    </flux:modal>

</div>
