<div class="space-y-5">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Cleaning Log</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Track all items sent to cleaning and their return times.</p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Sent to Cleaning</p>
            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->stats['total_sent'] }}</p>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50/40 p-4 shadow-sm dark:border-emerald-800/30 dark:bg-emerald-900/10">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Returned</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $this->stats['total_returned'] }}</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50/40 p-4 shadow-sm dark:border-amber-800/30 dark:bg-amber-900/10">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Still in Cleaning</p>
            <p class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $this->stats['still_out'] }}</p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3 border-b border-zinc-200 bg-zinc-50/60 px-5 py-3 dark:border-zinc-700/60 dark:bg-zinc-800/40">
            <div class="relative w-56">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search item, variant or booking…"
                    class="w-full rounded-lg border border-zinc-200 bg-white py-2 pl-9 pr-3 text-sm text-zinc-900 placeholder-zinc-400 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder-zinc-500"
                />
            </div>
            <div>
                <input
                    wire:model.live="dateFrom"
                    type="date"
                    class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                />
            </div>
            <span class="text-zinc-400 text-xs">to</span>
            <div>
                <input
                    wire:model.live="dateTo"
                    type="date"
                    class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                />
            </div>
            <div class="flex gap-1.5">
                <button wire:click="$set('statusFilter', 'all')"
                    class="rounded-md px-2.5 py-1 text-xs transition-colors bg-zinc-100 text-zinc-700 ring-zinc-200 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-700 {{ $statusFilter === 'all' ? 'ring-2 font-semibold' : 'ring-1' }}">
                    All
                </button>
                <button wire:click="$set('statusFilter', 'in_cleaning')"
                    class="rounded-md px-2.5 py-1 text-xs transition-colors bg-amber-50 text-amber-700 ring-amber-100 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:ring-amber-800/30 {{ $statusFilter === 'in_cleaning' ? 'ring-2 font-semibold' : 'ring-1' }}">
                    In Cleaning
                </button>
                <button wire:click="$set('statusFilter', 'returned')"
                    class="rounded-md px-2.5 py-1 text-xs transition-colors bg-emerald-50 text-emerald-700 ring-emerald-100 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:ring-emerald-800/30 {{ $statusFilter === 'returned' ? 'ring-2 font-semibold' : 'ring-1' }}">
                    Returned
                </button>
            </div>
            <span class="ml-auto text-xs font-medium text-zinc-400 dark:text-zinc-500">
                {{ $this->logs->total() }} {{ Str::plural('entry', $this->logs->total()) }}
            </span>
        </div>

        {{-- Table --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Item / Variant</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Booking</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Sent</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Returned</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Duration</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @forelse ($this->logs as $log)
                    @php
                        $itemName = $log->inventoryItem?->name ?? 'Unknown Item';
                        $hasVariant = $log->variant !== null;
                        $isReturned = $log->returned_from_cleaning_at !== null;

                        if ($isReturned && $log->returned_from_cleaning_at && $log->sent_to_cleaning_at) {
                            $duration = $log->sent_to_cleaning_at->diffInMinutes($log->returned_from_cleaning_at);
                            $hours = intdiv($duration, 60);
                            $mins = $duration % 60;
                            $durationLabel = $hours > 0 ? "{$hours}h {$mins}m" : "{$mins}m";
                        } else {
                            $durationLabel = '—';
                        }
                    @endphp
                    <tr class="group transition-colors hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30" wire:key="log-{{ $log->id }}">
                        <td class="px-5 py-3.5">
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $hasVariant && $log->variant->label ? "{$itemName} ({$log->variant->label})" : $itemName }}
                            </div>
                            @php
                                $sku = $hasVariant ? $log->variant->sku : ($log->inventoryItem?->sku ?? null);
                            @endphp
                            @if($sku)
                                <div class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">
                                    <code class="rounded bg-zinc-100 px-1.5 py-0.5 font-mono dark:bg-zinc-800">{{ $sku }}</code>
                                    @if($log->quantity > 1)
                                        &times;{{ $log->quantity }}
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($log->booking)
                                <a href="{{ route('bookings.show', $log->booking_id) }}" wire:navigate
                                    class="font-mono text-sm font-semibold text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300">
                                    {{ $log->booking->booking_number }}
                                </a>
                                @if($log->booking->customer)
                                    <div class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">{{ $log->booking->customer->name }}</div>
                                @endif
                            @else
                                <span class="text-zinc-300 dark:text-zinc-600">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="text-zinc-700 dark:text-zinc-300">{{ $log->sent_to_cleaning_at->format('d M Y, H:i') }}</span>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($isReturned)
                                <span class="text-emerald-600 dark:text-emerald-400">{{ $log->returned_from_cleaning_at->format('d M Y, H:i') }}</span>
                            @else
                                <span class="text-zinc-300 dark:text-zinc-600">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center tabular-nums">
                            @if($isReturned)
                                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $durationLabel }}</span>
                            @else
                                <span class="text-zinc-300 dark:text-zinc-600">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($isReturned)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400">
                                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                    Returned
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">
                                    <span class="size-1.5 rounded-full bg-amber-500"></span>
                                    In Cleaning
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right text-xs text-zinc-400 dark:text-zinc-500">
                            {{ $log->creator?->name ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                @if($search || $dateFrom || $dateTo || $statusFilter !== 'all') No cleaning logs match your filters
                                @else No items have been sent to cleaning yet. @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($this->logs->hasPages())
            <div class="border-t border-zinc-200 px-5 py-3 dark:border-zinc-700/60">
                {{ $this->logs->links() }}
            </div>
        @endif

    </div>

</div>
