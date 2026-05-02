<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('inventory.index') }}" wire:navigate
            class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 shadow-sm hover:bg-zinc-50 hover:text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $item->name }}</h1>
                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold
                    {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400' }}">
                    <span class="size-1.5 rounded-full {{ $item->is_active ? 'bg-emerald-500' : 'bg-zinc-400' }}"></span>
                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                {{ $item->category?->name ?? 'Uncategorised' }}
                @if($item->sku) &middot; <code class="text-xs">{{ $item->sku }}</code> @endif
            </p>
        </div>
    </div>

    {{-- Mini Dashboard --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-6">

        <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Bookings</p>
            <p class="mt-1 text-2xl font-bold tabular-nums text-zinc-900 dark:text-zinc-100">{{ $this->totalBookings }}</p>
        </div>

        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 shadow-sm dark:border-blue-800/30 dark:bg-blue-900/10">
            <p class="text-xs font-medium text-blue-700 dark:text-blue-400">Active Now</p>
            <p class="mt-1 text-2xl font-bold tabular-nums text-blue-700 dark:text-blue-300">{{ $this->activeBookings }}</p>
        </div>

        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm dark:border-emerald-800/30 dark:bg-emerald-900/10 sm:col-span-2">
            <p class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Total Revenue</p>
            <p class="mt-1 text-lg font-bold tabular-nums text-emerald-700 dark:text-emerald-300">UGX {{ number_format($this->totalRevenue, 0) }}</p>
        </div>

        {{-- Stock: Total owned --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Stock</p>
            <p class="mt-1 text-2xl font-bold tabular-nums text-zinc-900 dark:text-zinc-100">{{ $this->effectiveStock }}</p>
            <p class="mt-0.5 text-xs text-zinc-400">Physical owned</p>
        </div>

        {{-- Available: in-shop right now --}}
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm dark:border-amber-800/30 dark:bg-amber-900/10">
            <p class="text-xs font-medium text-amber-700 dark:text-amber-400">Available</p>
            <p class="mt-1 text-2xl font-bold tabular-nums text-amber-700 dark:text-amber-300">{{ $this->effectiveAvailable }}</p>
            <p class="mt-0.5 text-xs text-amber-600/60 dark:text-amber-500">Bookable now</p>
        </div>

    </div>

    @if($this->variantTypes->isEmpty())
        <div class="rounded-xl border border-dashed border-amber-300 bg-amber-50 px-5 py-4 text-sm text-amber-700 dark:border-amber-800/40 dark:bg-amber-900/10 dark:text-amber-400">
            No variant types defined yet. Go to
            <a href="{{ route('inventory.index') }}" wire:navigate class="font-semibold underline">Inventory → Variant Types</a>
            to create types like "Size" or "Color" before adding variations here.
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Variants Panel --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                    <div>
                        <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Variations</h2>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500">Each variation is a unique combination of attribute values (e.g. Size: S + Color: Red)</p>
                    </div>
                    @can('inventory.edit')
                    @if($this->variantTypes->isNotEmpty())
                    <button wire:click="openCreateVariant"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-semibold text-black shadow-sm hover:bg-amber-400 transition-colors">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Variation
                    </button>
                    @endif
                    @endcan
                </div>

                @if($this->variants->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No variations yet</p>
                        <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">Add a variation by selecting attribute values (e.g. Size=S, Color=Red).</p>
                    </div>
                @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Variation</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">SKU</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Hire Price</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Stock / Avail</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                            @foreach($this->variants as $variant)
                                <tr class="group transition-colors hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30" wire:key="variant-{{ $variant->id }}">
                                    <td class="px-5 py-3.5">
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $variant->name }}</div>
                                        @if($variant->attributeValues->isNotEmpty())
                                            <div class="mt-0.5 flex flex-wrap gap-1">
                                                @foreach($variant->attributeValues as $av)
                                                    <span class="inline-flex items-center rounded bg-zinc-100 px-1.5 py-0.5 text-xs text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                                        {{ $av->type->name }}: {{ $av->label }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($variant->sku)
                                            <code class="rounded bg-zinc-100 px-2 py-0.5 text-xs font-mono text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $variant->sku }}</code>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right tabular-nums">
                                        @if($variant->rental_price !== null)
                                            <span class="font-semibold text-zinc-900 dark:text-zinc-100">UGX {{ number_format($variant->rental_price, 0) }}</span>
                                        @else
                                            <span class="text-xs text-zinc-400 dark:text-zinc-500">base price</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <div class="flex items-center justify-center gap-1 text-xs">
                                            <span class="inline-flex items-center justify-center min-w-[2rem] rounded-full bg-zinc-100 px-2 py-0.5 font-semibold tabular-nums text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300" title="Total stock">
                                                {{ $variant->stock_quantity }}
                                            </span>
                                            <span class="text-zinc-300 dark:text-zinc-600">/</span>
                                            <span class="inline-flex items-center justify-center min-w-[2rem] rounded-full bg-amber-50 px-2 py-0.5 font-semibold tabular-nums text-amber-700 dark:bg-amber-900/20 dark:text-amber-400" title="Available">
                                                {{ $variant->available_quantity }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <button wire:click="toggleVariantActive({{ $variant->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold transition-colors
                                                {{ $variant->is_active
                                                    ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400'
                                                    : 'bg-zinc-100 text-zinc-500 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                            <span class="size-1.5 rounded-full {{ $variant->is_active ? 'bg-emerald-500' : 'bg-zinc-400' }}"></span>
                                            {{ $variant->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                            @can('inventory.edit')
                                            <button wire:click="openEditVariant({{ $variant->id }})"
                                                class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors" title="Edit">
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            @endcan
                                            @can('inventory.delete')
                                            <button wire:click="openDeleteVariant({{ $variant->id }})"
                                                class="rounded-lg p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors" title="Delete">
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-zinc-200 bg-zinc-50/60 dark:border-zinc-700/60 dark:bg-zinc-800/40">
                                <td colspan="3" class="px-5 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400">Totals</td>
                                <td class="px-5 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1 text-xs">
                                        <span class="font-bold tabular-nums text-zinc-900 dark:text-zinc-100">{{ $this->variants->sum('stock_quantity') }}</span>
                                        <span class="text-zinc-300">/</span>
                                        <span class="font-bold tabular-nums text-amber-700 dark:text-amber-400">{{ $this->variants->sum('available_quantity') }}</span>
                                    </div>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>

            {{-- Recent Bookings --}}
            @if($this->recentBookings->isNotEmpty())
            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Recent Bookings</h2>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Booking</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Customer</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Variation</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                        @foreach($this->recentBookings as $bookingItem)
                            @php
                                $statusColors = [
                                    'draft' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300',
                                    'confirmed' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
                                    'active' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
                                    'completed' => 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400',
                                    'cancelled' => 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400',
                                ];
                            @endphp
                            <tr wire:key="rbi-{{ $bookingItem->id }}">
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('bookings.show', $bookingItem->booking) }}" wire:navigate
                                        class="font-mono text-xs font-semibold text-amber-600 hover:underline dark:text-amber-400">
                                        {{ $bookingItem->booking->booking_number }}
                                    </a>
                                </td>
                                <td class="px-5 py-3.5 text-zinc-700 dark:text-zinc-300">{{ $bookingItem->booking->customer->name }}</td>
                                <td class="px-5 py-3.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $bookingItem->variant?->name ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusColors[$bookingItem->booking->status] ?? 'bg-zinc-100 text-zinc-600' }}">
                                        {{ ucfirst($bookingItem->booking->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">
                                    UGX {{ number_format($bookingItem->subtotal, 0) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Product Info</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-medium text-zinc-400 dark:text-zinc-500">Hire Rate</dt>
                        <dd class="mt-0.5 font-semibold text-zinc-900 dark:text-zinc-100">UGX {{ number_format($item->base_rental_price, 0) }}</dd>
                    </div>
                    @if($item->cost_price)
                    <div>
                        <dt class="text-xs font-medium text-zinc-400 dark:text-zinc-500">Cost Price</dt>
                        <dd class="mt-0.5 font-semibold text-zinc-900 dark:text-zinc-100">UGX {{ number_format($item->cost_price, 0) }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-zinc-400 dark:text-zinc-500">Category</dt>
                        <dd class="mt-0.5 text-zinc-700 dark:text-zinc-300">{{ $item->category?->name ?? '—' }}</dd>
                    </div>
                    @if($item->sku)
                    <div>
                        <dt class="text-xs font-medium text-zinc-400 dark:text-zinc-500">SKU</dt>
                        <dd class="mt-0.5"><code class="rounded bg-zinc-100 px-2 py-0.5 text-xs font-mono text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $item->sku }}</code></dd>
                    </div>
                    @endif
                    @if($item->description)
                    <div>
                        <dt class="text-xs font-medium text-zinc-400 dark:text-zinc-500">Description</dt>
                        <dd class="mt-0.5 text-zinc-600 dark:text-zinc-400">{{ $item->description }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between border-t border-zinc-100 pt-3 dark:border-zinc-700/60">
                        <div>
                            <dt class="text-xs font-medium text-zinc-400 dark:text-zinc-500">Total Stock</dt>
                            <dd class="mt-0.5 text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $this->effectiveStock }}</dd>
                        </div>
                        <div class="text-right">
                            <dt class="text-xs font-medium text-amber-600 dark:text-amber-400">Available</dt>
                            <dd class="mt-0.5 text-lg font-bold text-amber-700 dark:text-amber-300">{{ $this->effectiveAvailable }}</dd>
                        </div>
                    </div>
                </dl>
                <div class="mt-4 border-t border-zinc-100 pt-4 dark:border-zinc-700/60">
                    <a href="{{ route('bookings.create') }}" wire:navigate
                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-black hover:bg-amber-400 transition-colors">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        New Booking
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- Variant Form Modal --}}
    <flux:modal name="variant-form" class="md:w-[34rem]">
        <form wire:submit="saveVariant" class="space-y-5">
            <div class="border-b border-zinc-100 pb-4 dark:border-zinc-700">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $editingVariantId ? 'Edit Variation' : 'New Variation' }}</h3>
                <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Select one value per attribute type to define this variation.</p>
            </div>

            {{-- Attribute value selectors --}}
            <div class="space-y-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Attributes <span class="text-red-400">*</span></p>
                @if($this->variantTypes->isEmpty())
                    <p class="text-xs text-zinc-400">No variant types defined. Go to Inventory → Variant Types first.</p>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($this->variantTypes as $vt)
                            <div>
                                <label class="mb-1 block text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ $vt->name }}</label>
                                <select wire:model="variantAttributes.{{ $vt->id }}"
                                    class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                                    <option value="">— none —</option>
                                    @foreach($vt->values as $val)
                                        <option value="{{ $val->id }}">{{ $val->label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                    @error('variantAttributes') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Hire Price (UGX)</label>
                    <flux:input wire:model="variantRentalPrice" type="number" min="0" step="1" placeholder="Leave blank to inherit" />
                    <p class="mt-1 text-xs text-zinc-400">Base: UGX {{ number_format($item->base_rental_price, 0) }}</p>
                    <flux:error name="variantRentalPrice" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Cost Price (UGX)</label>
                    <flux:input wire:model="variantCostPrice" type="number" min="0" step="1" placeholder="Leave blank to inherit" />
                    @if($item->cost_price)
                        <p class="mt-1 text-xs text-zinc-400">Base: UGX {{ number_format($item->cost_price, 0) }}</p>
                    @endif
                    <flux:error name="variantCostPrice" />
                </div>
                @if(!$editingVariantId)
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Total Stock</label>
                        <flux:input wire:model="variantStock" type="number" min="0" step="1" placeholder="0" />
                        <flux:error name="variantStock" />
                    </div>
                @endif
{{--                <div>--}}
{{--                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Available for Booking</label>--}}
{{--                    <flux:input wire:model="variantAvailableQty" type="number" min="0" step="1" placeholder="0" />--}}
{{--                    <flux:error name="variantAvailableQty" />--}}
{{--                </div>--}}
{{--                <div class="col-span-2">--}}
{{--                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">SKU <span class="text-zinc-400 text-xs font-normal">(auto-generated if left blank)</span></label>--}}
{{--                    <flux:input wire:model="variantSku" placeholder="Leave blank to auto-generate" />--}}
{{--                    <flux:error name="variantSku" />--}}
{{--                </div>--}}
            </div>

            <div>
                <label class="flex cursor-pointer items-center gap-2.5">
                    <flux:checkbox wire:model="variantIsActive" />
                    <span class="text-sm text-zinc-700 dark:text-zinc-300">Active (available for selection on bookings)</span>
                </label>
            </div>

            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('variant-form').close()" variant="ghost" type="button">Cancel</flux:button>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-black hover:bg-amber-400 transition-colors">
                    {{ $editingVariantId ? 'Save Changes' : 'Create Variation' }}
                </button>
            </div>
        </form>
    </flux:modal>

    {{-- Confirm Delete Variant Modal --}}
    <flux:modal name="confirm-delete-variant" class="md:w-96">
        <div class="space-y-5">
            <div class="flex items-start gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="size-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Delete Variation</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">This variation will be removed. Existing booking records are preserved.</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('confirm-delete-variant').close()" variant="ghost" type="button">Cancel</flux:button>
                <button wire:click="deleteVariant" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500 transition-colors">Delete</button>
            </div>
        </div>
    </flux:modal>

</div>
