<div class="space-y-5">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                @if(request()->routeIs('rental-operations.checked-out')) Checked Out Items
                @elseif(request()->routeIs('rental-operations.in-cleaning')) Items In Cleaning
                @else Items Not In Stock @endif
            </h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                @if(request()->routeIs('rental-operations.checked-out')) Rental items currently with customers.
                @elseif(request()->routeIs('rental-operations.in-cleaning')) Rental items being cleaned or prepped.
                @else View all rental items currently not in stock. @endif
            </p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-amber-200 bg-amber-50/40 p-4 shadow-sm dark:border-amber-800/30 dark:bg-amber-900/10">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30">
                    <svg class="size-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Checked Out</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->checkedOutCount }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-sky-200 bg-sky-50/40 p-4 shadow-sm dark:border-sky-800/30 dark:bg-sky-900/10">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-sky-100 dark:bg-sky-900/30">
                    <svg class="size-5 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">In Cleaning</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->inCleaningCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3 border-b border-zinc-200 bg-zinc-50/60 px-5 py-3 dark:border-zinc-700/60 dark:bg-zinc-800/40">
            <div class="relative w-64">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search item or customer…"
                    class="w-full rounded-lg border border-zinc-200 bg-white py-2 pl-9 pr-3 text-sm text-zinc-900 placeholder-zinc-400 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder-zinc-500"
                />
            </div>
            <div class="flex gap-1.5">
                <button wire:click="$set('statusFilter', ['checked_out', 'in_cleaning'])"
                    class="rounded-md px-2.5 py-1 text-xs transition-colors bg-zinc-100 text-zinc-700 ring-zinc-200 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-700 {{ count($statusFilter) === 2 ? 'ring-2 font-semibold' : 'ring-1' }}">
                    All
                </button>
                <button wire:click="toggleStatus('checked_out')"
                    class="rounded-md px-2.5 py-1 text-xs transition-colors bg-amber-50 text-amber-700 ring-amber-100 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:ring-amber-800/30 {{ in_array('checked_out', $statusFilter) ? 'ring-2 font-semibold' : 'ring-1' }}">
                    Checked Out
                </button>
                <button wire:click="toggleStatus('in_cleaning')"
                    class="rounded-md px-2.5 py-1 text-xs transition-colors bg-sky-50 text-sky-700 ring-sky-100 hover:bg-sky-100 dark:bg-sky-900/20 dark:text-sky-400 dark:ring-sky-800/30 {{ in_array('in_cleaning', $statusFilter) ? 'ring-2 font-semibold' : 'ring-1' }}">
                    In Cleaning
                </button>
            </div>
            <span class="ml-auto text-xs font-medium text-zinc-400 dark:text-zinc-500">
                {{ $this->items->total() }} {{ Str::plural('item', $this->items->total()) }}
            </span>
        </div>

        {{-- Table --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Item / Variant</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Booking / Customer</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Time</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @forelse ($this->items as $item)
                    @php
                        $statusCfg = [
                            'checked_out' => ['pill' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400', 'dot' => 'bg-amber-500', 'label' => 'Checked Out'],
                            'in_cleaning' => ['pill' => 'bg-sky-50 text-sky-700 dark:bg-sky-900/20 dark:text-sky-400', 'dot' => 'bg-sky-500', 'label' => 'In Cleaning'],
                        ];
                        $cfg = $statusCfg[$item->status] ?? $statusCfg['checked_out'];
                    @endphp
                    <tr class="group transition-colors hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30" wire:key="item-{{ $item->id }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                    <svg class="size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        @php
                                            $itemName = $item->inventoryItem?->name ?? 'Unknown Item';
                                            $hasVariant = $item->variant !== null;
                                        @endphp
                                        {{ $hasVariant && $item->variant->label ? "{$itemName} ({$item->variant->label})" : $itemName }}
                                    </div>
                                    @php
                                        $sku = $hasVariant ? $item->variant->sku : ($item->inventoryItem?->sku ?? null);
                                    @endphp
                                    @if($sku)
                                        <div class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">
                                            <code class="rounded bg-zinc-100 px-1.5 py-0.5 font-mono dark:bg-zinc-800">{{ $sku }}</code>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($item->booking)
                                <a href="{{ route('bookings.show', $item->booking_id) }}" wire:navigate
                                    class="font-mono text-sm font-semibold text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300">
                                    {{ $item->booking->booking_number }}
                                </a>
                                @if($item->booking->customer)
                                    <div class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">
                                        {{ $item->booking->customer->name }}
                                    </div>
                                @endif
                            @else
                                <span class="text-zinc-300 dark:text-zinc-600">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $cfg['pill'] }}">
                                <span class="size-1.5 rounded-full {{ $cfg['dot'] }}"></span>
                                {{ $cfg['label'] }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($item->checked_out_at && $item->status === 'checked_out')
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    <span class="font-medium text-zinc-700 dark:text-zinc-300">Out:</span>
                                    {{ $item->checked_out_at->format('d M Y, H:i') }}
                                </div>
                            @elseif($item->checked_out_at)
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    <span class="font-medium text-zinc-700 dark:text-zinc-300">Returned:</span>
                                    {{ $item->returned_at?->format('d M Y, H:i') ?? '—' }}
                                </div>
                            @else
                                <span class="text-zinc-300 dark:text-zinc-600">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('bookings.show', $item->booking_id) }}" wire:navigate
                                    class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors"
                                    title="View Booking">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                @if($search) No items match "{{ $search }}"
                                @else All items are returned and in stock. @endif
                            </p>
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

</div>
