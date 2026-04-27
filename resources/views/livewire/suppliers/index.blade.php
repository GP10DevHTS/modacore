<div class="space-y-5">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Suppliers</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Manage suppliers for inventory procurement.</p>
        </div>
        @can('inventory.create')
        <button wire:click="openCreate"
            class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-black shadow-sm hover:bg-amber-400 active:bg-amber-600 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add Supplier
        </button>
        @endcan
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">

        {{-- Toolbar --}}
        <div class="flex items-center justify-between gap-4 border-b border-zinc-200 bg-zinc-50/60 px-5 py-3 dark:border-zinc-700/60 dark:bg-zinc-800/40">
            <div class="relative w-72">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search suppliers…"
                    class="w-full rounded-lg border border-zinc-200 bg-white py-2 pl-9 pr-3 text-sm text-zinc-900 placeholder-zinc-400 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder-zinc-500"/>
            </div>
            <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500">{{ $this->suppliers->total() }} {{ Str::plural('supplier', $this->suppliers->total()) }}</span>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Supplier</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Contact</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Orders</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @forelse($this->suppliers as $supplier)
                    <tr class="group transition-colors hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30" wire:key="supplier-{{ $supplier->id }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-xs font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                    {{ strtoupper(substr($supplier->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $supplier->name }}</div>
                                    @if($supplier->contact_person)
                                        <div class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">{{ $supplier->contact_person }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($supplier->phone)
                                <div class="text-zinc-800 dark:text-zinc-200">{{ $supplier->phone }}</div>
                            @endif
                            @if($supplier->email)
                                <div class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">{{ $supplier->email }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('purchase-orders.index', ['supplier' => $supplier->id]) }}" wire:navigate
                                class="inline-flex items-center justify-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-semibold text-zinc-600 hover:bg-amber-100 hover:text-amber-700 dark:bg-zinc-800 dark:text-zinc-300 transition-colors">
                                {{ $supplier->purchase_orders_count }}
                            </a>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                @can('inventory.edit')
                                <button wire:click="openEdit({{ $supplier->id }})"
                                    class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors" title="Edit">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @endcan
                                @can('inventory.delete')
                                <button wire:click="openDelete({{ $supplier->id }})"
                                    class="rounded-lg p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors" title="Delete">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-16 text-center">
                            <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                @if($search) No suppliers match "{{ $search }}" @else No suppliers yet @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($this->suppliers->hasPages())
            <div class="border-t border-zinc-200 px-5 py-3 dark:border-zinc-700/60">{{ $this->suppliers->links() }}</div>
        @endif
    </div>

    {{-- Supplier Form Modal --}}
    <flux:modal name="supplier-form" class="md:w-[36rem]">
        <form wire:submit="save" class="space-y-5">
            <div class="border-b border-zinc-100 pb-4 dark:border-zinc-700">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $editingId ? 'Edit Supplier' : 'Add Supplier' }}</h3>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Company Name <span class="text-red-500">*</span></label>
                    <flux:input wire:model="name" placeholder="e.g. Kampala Fabrics Ltd" />
                    <flux:error name="name" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Contact Person</label>
                    <flux:input wire:model="contactPerson" placeholder="Primary contact name" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Phone</label>
                    <flux:input wire:model="phone" placeholder="+256 700 000 000" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Email</label>
                    <flux:input wire:model="email" type="email" placeholder="supplier@example.com" />
                    <flux:error name="email" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Address</label>
                    <flux:input wire:model="address" placeholder="Physical address" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Notes</label>
                    <flux:textarea wire:model="notes" rows="2" placeholder="Optional notes…" />
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('supplier-form').close()" variant="ghost" type="button">Cancel</flux:button>
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-black hover:bg-amber-400 active:bg-amber-600 transition-colors">
                    {{ $editingId ? 'Save Changes' : 'Add Supplier' }}
                </button>
            </div>
        </form>
    </flux:modal>

    {{-- Confirm Delete Modal --}}
    <flux:modal name="confirm-delete-supplier" class="md:w-96">
        <div class="space-y-5">
            <div class="flex items-start gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="size-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Delete Supplier</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">This will remove the supplier. Purchase orders will be unaffected.</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('confirm-delete-supplier').close()" variant="ghost" type="button">Cancel</flux:button>
                <button wire:click="delete"
                    class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500 transition-colors">
                    Delete Supplier
                </button>
            </div>
        </div>
    </flux:modal>

</div>
