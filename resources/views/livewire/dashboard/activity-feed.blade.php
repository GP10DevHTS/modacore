<div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">

    <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Recent Activity</h3>
        <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">System-wide events across all modules.</p>
    </div>

    @forelse($this->activities as $event)
        <div class="flex items-start gap-3 border-b border-zinc-100 px-5 py-3.5 last:border-0 dark:border-zinc-700/50"
             wire:key="af-{{ $loop->index }}">

            {{-- Icon --}}
            <div class="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-full {{ $event['icon_color'] }}">
                @if($event['type'] === 'booking')
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                @elseif($event['type'] === 'payment')
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                @else
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                @endif
            </div>

            {{-- Content --}}
            <div class="min-w-0 flex-1">
                @if($event['url'] !== '#')
                    <a href="{{ $event['url'] }}" wire:navigate
                        class="text-sm font-medium text-zinc-900 hover:text-amber-600 dark:text-zinc-100 dark:hover:text-amber-400 transition-colors">
                        {{ $event['title'] }}
                    </a>
                @else
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $event['title'] }}</p>
                @endif
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ $event['description'] }}</p>
            </div>

            {{-- Time + Actor --}}
            <div class="shrink-0 text-right">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $event['time']?->diffForHumans() }}</p>
                @if($event['actor'])
                    <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">{{ $event['actor'] }}</p>
                @endif
            </div>
        </div>
    @empty
        <div class="px-5 py-10 text-center">
            <svg class="mx-auto mb-2 size-8 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm font-medium text-zinc-400 dark:text-zinc-500">No recent activity</p>
        </div>
    @endforelse

</div>
