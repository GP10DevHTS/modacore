<div class="space-y-5">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Stock &amp; Materials</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Manage inventory items and categories.</p>
        </div>
        @if($activeTab === 'items')
            @can('inventory.create')
            <button wire:click="openCreate"
                class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-black shadow-sm hover:bg-amber-400 active:bg-amber-600 transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Item
            </button>
            @endcan
        @elseif($activeTab === 'categories')
            @can('inventory.create')
            <button wire:click="openCreateCategory"
                class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-black shadow-sm hover:bg-amber-400 active:bg-amber-600 transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Category
            </button>
            @endcan
        @elseif($activeTab === 'variants')
            @can('inventory.create')
            <button wire:click="openCreateVariantType"
                class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-black shadow-sm hover:bg-amber-400 active:bg-amber-600 transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Variant Type
            </button>
            @endcan
        @endif
    </div>

    {{-- Tabs --}}
    <div class="flex gap-0 border-b border-zinc-200 dark:border-zinc-700/60">
        @foreach(['items' => 'Items', 'categories' => 'Categories', 'variants' => 'Variant Types'] as $tab => $label)
            <button wire:click="switchTab('{{ $tab }}')"
                class="relative px-5 py-2.5 text-sm font-medium transition-colors
                    {{ $activeTab === $tab
                        ? 'text-amber-600 dark:text-amber-400 after:absolute after:inset-x-0 after:bottom-0 after:h-0.5 after:bg-amber-500'
                        : 'text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ═══ ITEMS TAB ═══ --}}
    @if($activeTab === 'items')
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">

            <div class="flex items-center justify-between gap-4 border-b border-zinc-200 bg-zinc-50/60 px-5 py-3 dark:border-zinc-700/60 dark:bg-zinc-800/40">
                <div class="relative w-72">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name or SKU…"
                        class="w-full rounded-lg border border-zinc-200 bg-white py-2 pl-9 pr-3 text-sm text-zinc-900 placeholder-zinc-400 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder-zinc-500"/>
                </div>
                <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500 whitespace-nowrap">
                    {{ $this->items->total() }} {{ Str::plural('item', $this->items->total()) }}
                </span>
            </div>

            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Item</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Category</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">SKU</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Stock</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Rate / Hire</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                    @forelse ($this->items as $item)
                        <tr class="group transition-colors hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30" wire:key="item-{{ $item->id }}">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                        <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $item->name }}</div>
                                        @if($item->description)
                                            <div class="mt-0.5 max-w-xs truncate text-xs text-zinc-400 dark:text-zinc-500">{{ $item->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($item->category)
                                    <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                        {{ $item->category->name }}
                                    </span>
                                @else
                                    <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if($item->sku)
                                    <code class="rounded bg-zinc-100 px-2 py-0.5 text-xs font-mono tracking-wide text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $item->sku }}</code>
                                @else
                                    <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center justify-center min-w-[2rem] rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-semibold tabular-nums text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    {{ $item->stock_quantity }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">
                                UGX {{ number_format($item->base_rental_price, 0) }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <button wire:click="toggleActive({{ $item->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold transition-colors
                                        {{ $item->is_active
                                            ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400'
                                            : 'bg-zinc-100 text-zinc-500 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                    <span class="size-1.5 rounded-full {{ $item->is_active ? 'bg-emerald-500' : 'bg-zinc-400' }}"></span>
                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-100 transition-opacity group-hover:opacity-100">
                                    <a href="{{ route('inventory.show', $item) }}" wire:navigate
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors" title="View">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @can('inventory.edit')
                                    <button wire:click="openEdit({{ $item->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors" title="Edit">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @endcan
                                    @can('inventory.delete')
                                    <button wire:click="openDeleteItem({{ $item->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors" title="Delete">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                    @if($search) No items match "{{ $search }}" @else No inventory items yet @endif
                                </p>
                                @if(!$search)<p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">Add categories first, then create items.</p>@endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($this->items->hasPages())
                <div class="border-t border-zinc-200 px-5 py-3 dark:border-zinc-700/60">
                    {{ $this->items->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- ═══ CATEGORIES TAB ═══ --}}
    @if($activeTab === 'categories')
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">

            <div class="flex items-center justify-between border-b border-zinc-200 bg-zinc-50/60 px-5 py-3 dark:border-zinc-700/60 dark:bg-zinc-800/40">
                <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500">
                    {{ $this->categories->count() }} {{ Str::plural('category', $this->categories->count()) }}
                </span>
            </div>

            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Description</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Items</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                    @forelse ($this->categories as $category)
                        <tr class="group transition-colors hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30" wire:key="cat-{{ $category->id }}">
                            <td class="px-5 py-3.5 font-semibold text-zinc-900 dark:text-zinc-100">{{ $category->name }}</td>
                            <td class="px-5 py-3.5 text-zinc-500 dark:text-zinc-400">{{ $category->description ?: '—' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center justify-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                    {{ $category->items_count }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-100 transition-opacity group-hover:opacity-100">
                                    @can('inventory.edit')
                                    <button wire:click="openEditCategory({{ $category->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors" title="Edit">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @endcan
                                    @can('inventory.delete')
                                    <button wire:click="openDeleteCategory({{ $category->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors" title="Delete">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-16 text-center">
                                <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No categories yet</p>
                                <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">Add a category before creating inventory items.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    {{-- ═══ VARIANT TYPES TAB ═══ --}}
    @if($activeTab === 'variants')
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">

            <div class="flex items-center justify-between border-b border-zinc-200 bg-zinc-50/60 px-5 py-3 dark:border-zinc-700/60 dark:bg-zinc-800/40">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    Define variant dimensions (e.g. Size, Color) and their values. Products use these to create specific variations.
                </p>
                <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500 whitespace-nowrap">
                    {{ $this->variantTypes->count() }} {{ Str::plural('type', $this->variantTypes->count()) }}
                </span>
            </div>

            @if($this->variantTypes->isEmpty())
                <div class="px-5 py-16 text-center">
                    <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No variant types yet</p>
                    <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">Create types like "Size" or "Color", then add their values.</p>
                </div>
            @else
                <div class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                    @foreach($this->variantTypes as $vt)
                        <div class="px-5 py-4" wire:key="vt-{{ $vt->id }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">{{ $vt->name }}</span>
                                    <span class="ml-2 text-xs text-zinc-400 dark:text-zinc-500">{{ $vt->values_count }} {{ Str::plural('value', $vt->values_count) }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button wire:click="openManageValues({{ $vt->id }})"
                                        class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-amber-600 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-900/20 transition-colors">
                                        Manage Values
                                    </button>
                                    @can('inventory.edit')
                                    <button wire:click="openEditVariantType({{ $vt->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors" title="Edit">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @endcan
                                    @can('inventory.delete')
                                    <button wire:click="openDeleteVariantType({{ $vt->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors" title="Delete">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Variant Type Form Modal --}}
    <flux:modal name="variant-type-form" class="md:w-[24rem]">
        <form wire:submit="saveVariantType" class="space-y-5">
            <div class="border-b border-zinc-100 pb-4 dark:border-zinc-700">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $editingVariantTypeId ? 'Edit Variant Type' : 'New Variant Type' }}</h3>
                <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">e.g. "Size", "Color", "Material"</p>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Name <span class="text-red-500">*</span></label>
                <flux:input wire:model="vtName" placeholder="e.g. Size" />
                <flux:error name="vtName" />
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('variant-type-form').close()" variant="ghost" type="button">Cancel</flux:button>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-black hover:bg-amber-400 transition-colors">
                    {{ $editingVariantTypeId ? 'Save Changes' : 'Create' }}
                </button>
            </div>
        </form>
    </flux:modal>

    {{-- Confirm Delete Variant Type Modal --}}
    <flux:modal name="confirm-delete-variant-type" class="md:w-96">
        <div class="space-y-5">
            <div class="flex items-start gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="size-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Delete Variant Type</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">All values under this type will also be deleted. This cannot be undone.</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('confirm-delete-variant-type').close()" variant="ghost" type="button">Cancel</flux:button>
                <button wire:click="deleteVariantType" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500 transition-colors">Delete</button>
            </div>
        </div>
    </flux:modal>

    {{-- Manage Values Modal --}}
    <flux:modal name="variant-values-modal" class="md:w-[32rem]">
        <div class="space-y-5">
            <div class="border-b border-zinc-100 pb-4 dark:border-zinc-700">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $this->managingVariantType?->name ?? 'Variant' }} — Values
                </h3>
                <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Add or remove values for this variant type.</p>
            </div>

            {{-- Add / Edit form --}}
            <form wire:submit="saveVariantTypeValue" class="flex gap-2">
                <div class="flex-1">
                    <flux:input wire:model="vtvLabel" placeholder="{{ $editingValueId ? 'Edit value…' : 'e.g. Small, Red, Cotton…' }}" />
                    <flux:error name="vtvLabel" />
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-black hover:bg-amber-400 transition-colors shrink-0">
                    {{ $editingValueId ? 'Update' : 'Add' }}
                </button>
                @if($editingValueId)
                <button type="button" wire:click="cancelEditValue"
                    class="rounded-lg border border-zinc-200 px-3 py-2 text-sm text-zinc-500 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800 transition-colors">
                    Cancel
                </button>
                @endif
            </form>

            {{-- Values list --}}
            @if($this->managingVariantType?->values->isNotEmpty())
                <div class="divide-y divide-zinc-100 rounded-lg border border-zinc-200 dark:divide-zinc-700/50 dark:border-zinc-700/60">
                    @foreach($this->managingVariantType->values as $value)
                        <div class="flex items-center justify-between px-4 py-2.5" wire:key="vtv-{{ $value->id }}">
                            <span class="text-sm text-zinc-800 dark:text-zinc-200">{{ $value->label }}</span>
                            <div class="flex items-center gap-1">
                                <button wire:click="editVariantTypeValue({{ $value->id }})"
                                    class="rounded p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 transition-colors">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="deleteVariantTypeValue({{ $value->id }})"
                                    wire:confirm="Delete '{{ $value->label }}'?"
                                    class="rounded p-1 text-zinc-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-sm text-zinc-400 dark:text-zinc-500 py-4">No values yet. Add one above.</p>
            @endif

            <div class="flex justify-end border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('variant-values-modal').close()" variant="ghost" type="button">Done</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Item Form Modal --}}
    <flux:modal name="item-form" class="md:w-[34rem]">
        <form wire:submit="saveItem" class="space-y-5">
            <div class="border-b border-zinc-100 pb-4 dark:border-zinc-700">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $editingId ? 'Edit Item' : 'New Inventory Item' }}</h3>
                <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ $editingId ? 'Update the details below.' : 'Fill in the details to add a new item.' }}</p>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Name <span class="text-red-500">*</span></label>
                    <flux:input wire:model="name" placeholder="e.g. Wedding Gown" />
                    <flux:error name="name" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Category <span class="text-red-500">*</span></label>
                    <select wire:model="categoryId"
                        class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                        <option value="">Select a category</option>
                        @foreach($this->categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <flux:error name="categoryId" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">SKU</label>
                    <flux:input wire:model="sku" placeholder="e.g. WG-001" />
                    <flux:error name="sku" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Hire Price (UGX) <span class="text-red-500">*</span></label>
                    <flux:input wire:model="baseRentalPrice" type="number" step="1" min="0" placeholder="0" />
                    <flux:error name="baseRentalPrice" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Cost Price (UGX)</label>
                    <flux:input wire:model="costPrice" type="number" step="1" min="0" placeholder="Purchase cost" />
                    <flux:error name="costPrice" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Stock Quantity <span class="text-red-500">*</span></label>
                    <flux:input wire:model="stockQuantity" type="number" min="0" step="1" placeholder="1" />
                    <flux:error name="stockQuantity" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Description</label>
                    <flux:textarea wire:model="description" rows="2" placeholder="Optional description…" />
                </div>
                <div class="sm:col-span-2">
                    <label class="flex cursor-pointer items-center gap-2.5">
                        <flux:checkbox wire:model="isActive" />
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">Active (available for booking)</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('item-form').close()" variant="ghost" type="button">Cancel</flux:button>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-black hover:bg-amber-400 transition-colors">
                    {{ $editingId ? 'Save Changes' : 'Create Item' }}
                </button>
            </div>
        </form>
    </flux:modal>

    {{-- Confirm Delete Item Modal --}}
    <flux:modal name="confirm-delete-item" class="md:w-96">
        <div class="space-y-5">
            <div class="flex items-start gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="size-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Delete Item</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">All associated variants will also be removed. This cannot be undone.</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('confirm-delete-item').close()" variant="ghost" type="button">Cancel</flux:button>
                <button wire:click="deleteItem" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500 transition-colors">Delete Item</button>
            </div>
        </div>
    </flux:modal>

    {{-- Category Form Modal --}}
    <flux:modal name="category-form" class="md:w-[28rem]">
        <form wire:submit="saveCategory" class="space-y-5">
            <div class="border-b border-zinc-100 pb-4 dark:border-zinc-700">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $editingCategoryId ? 'Edit Category' : 'New Category' }}</h3>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Name <span class="text-red-500">*</span></label>
                    <flux:input wire:model="catName" placeholder="e.g. Evening Wear" />
                    <flux:error name="catName" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Description</label>
                    <flux:textarea wire:model="catDescription" rows="2" placeholder="Optional description…" />
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('category-form').close()" variant="ghost" type="button">Cancel</flux:button>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-black hover:bg-amber-400 transition-colors">
                    {{ $editingCategoryId ? 'Save Changes' : 'Create Category' }}
                </button>
            </div>
        </form>
    </flux:modal>

    {{-- Confirm Delete Category Modal --}}
    <flux:modal name="confirm-delete-category" class="md:w-96">
        <div class="space-y-5">
            <div class="flex items-start gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="size-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Delete Category</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Categories with existing items cannot be deleted.</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('confirm-delete-category').close()" variant="ghost" type="button">Cancel</flux:button>
                <button wire:click="deleteCategory" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500 transition-colors">Delete Category</button>
            </div>
        </div>
    </flux:modal>

</div>
