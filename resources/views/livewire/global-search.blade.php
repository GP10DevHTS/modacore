<div class="relative" x-data="{ open: @entangle('showDropdown') }" @click.away="open = false">
    <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input
            wire:model.live.debounce.250ms="query"
            type="text"
            placeholder="Search by SKU…"
            autocomplete="off"
            class="w-56 rounded-lg border border-zinc-700/50 bg-zinc-800/80 py-1.5 pl-9 pr-3 text-xs text-zinc-100 placeholder-zinc-500 focus:border-amber-400/50 focus:outline-none focus:ring-1 focus:ring-amber-400/20 transition-colors"
        />
    </div>

    @if($showDropdown && strlen(trim($query)) >= 1)
        <div class="absolute left-0 top-full z-[60] mt-1 w-[32rem] overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-900">
            @php $results = $this->results; @endphp

            @if(count($results) > 0)
                <div class="max-h-96 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-700/50">
                    @foreach($results as $result)
                        <button wire:click="selectResult({{ $result['inventory_item_id'] }}, {{ $result['inventory_variant_id'] ?? 'null' }})"
                            class="w-full text-left px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors cursor-pointer">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        @if($result['type'] === 'variant')
                                            <span class="rounded bg-zinc-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">Variant</span>
                                        @endif
                                        <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 truncate">{{ $result['display_name'] }}</span>
                                    </div>
                                    <div class="mt-0.5 flex items-center gap-2 text-xs">
                                        @if($result['sku'])
                                            <code class="rounded bg-zinc-100 px-1 py-0.5 font-mono text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">{{ $result['sku'] }}</code>
                                        @endif
                                        <span class="font-medium tabular-nums text-amber-600 dark:text-amber-400">UGX {{ number_format($result['rental_price'], 0) }}</span>
                                    </div>
                                    <div class="mt-1 flex items-center gap-1.5">
                                        @php
                                            $loc = $result['location'];
                                            $colorMap = [
                                                'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
                                                'amber' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
                                                'sky' => 'bg-sky-50 text-sky-700 dark:bg-sky-900/20 dark:text-sky-400',
                                                'orange' => 'bg-orange-50 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400',
                                            ];
                                            $pillClass = $colorMap[$loc['color']] ?? 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400';
                                        @endphp
                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $pillClass }}">
                                            <span class="size-1.5 rounded-full {{ $loc['color'] === 'emerald' ? 'bg-emerald-500' : ($loc['color'] === 'amber' ? 'bg-amber-500' : ($loc['color'] === 'sky' ? 'bg-sky-500' : 'bg-orange-500')) }}"></span>
                                            {{ $loc['label'] }}
                                        </span>
                                    </div>
                                </div>
{{--                                <div class="shrink-0">--}}
{{--                                    <span class="inline-flex items-center gap-1 rounded-lg bg-amber-500/10 px-2.5 py-1.5 text-xs font-semibold text-amber-600 hover:bg-amber-500/20 dark:text-amber-400 transition-colors">--}}
{{--                                        <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">--}}
{{--                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>--}}
{{--                                        </svg>--}}
{{--                                        {{ $result['on_booking_page'] ? 'Add to Booking' : 'New Booking' }}--}}
{{--                                    </span>--}}
{{--                                </div>--}}
                            </div>
                        </button>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-6 text-center">
                    <svg class="mx-auto mb-2 size-6 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <p class="text-sm text-zinc-400 dark:text-zinc-500">No items or variants found for "{{ $query }}"</p>
                </div>
            @endif
        </div>
    @endif
</div>
