<div class="space-y-5">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Notifications</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                Your alerts and system messages.
                @if($this->unreadCount > 0)
                    <span class="font-medium text-amber-600 dark:text-amber-400">{{ $this->unreadCount }} unread.</span>
                @endif
            </p>
        </div>
        @if($this->unreadCount > 0)
            <button wire:click="markAllRead"
                class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Mark all read
            </button>
        @endif
    </div>

    {{-- Filter tabs --}}
    <div class="flex gap-1 border-b border-zinc-200 dark:border-zinc-700/60">
        @foreach(['all' => 'All', 'unread' => 'Unread', 'read' => 'Read'] as $value => $label)
            <button wire:click="$set('filter', '{{ $value }}')"
                class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px
                    {{ $filter === $value ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Notifications list --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
        @forelse($this->notifications as $notification)
            @php
                $data = $notification->data;
                $iconCfg = match($data['icon_type'] ?? 'info') {
                    'warning' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-600 dark:text-amber-400'],
                    'danger'  => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-600 dark:text-red-400'],
                    'success' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-600 dark:text-emerald-400'],
                    default   => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-600 dark:text-blue-400'],
                };
            @endphp
            <div class="group flex items-start gap-4 border-b border-zinc-100 px-5 py-4 last:border-0 dark:border-zinc-700/50 {{ $notification->read_at ? '' : 'bg-amber-50/40 dark:bg-amber-900/5' }}"
                 wire:key="cn-{{ $notification->id }}">

                {{-- Icon --}}
                <div class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-full {{ $iconCfg['bg'] }}">
                    @if(($data['icon_type'] ?? '') === 'warning')
                        <svg class="size-4 {{ $iconCfg['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    @else
                        <svg class="size-4 {{ $iconCfg['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    @endif
                </div>

                {{-- Content --}}
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            @if(!$notification->read_at)
                                <span class="mr-1.5 inline-block size-2 rounded-full bg-amber-500 align-middle"></span>
                            @endif
                            <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $data['title'] ?? 'Notification' }}</span>
                        </div>
                        <span class="shrink-0 text-xs text-zinc-400 dark:text-zinc-500">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-0.5 text-sm text-zinc-600 dark:text-zinc-400">{{ $data['message'] ?? '' }}</p>
                    @if(!empty($data['action_url']))
                        <a href="{{ $data['action_url'] }}" wire:navigate
                            class="mt-1.5 inline-flex items-center gap-1 text-xs font-medium text-amber-600 hover:text-amber-500 dark:text-amber-400 transition-colors">
                            View details
                            <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </a>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex shrink-0 items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    @if(!$notification->read_at)
                        <button wire:click="markRead('{{ $notification->id }}')"
                            class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors"
                            title="Mark as read">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    @endif
                    <button wire:click="delete('{{ $notification->id }}')"
                        wire:confirm="Delete this notification?"
                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors"
                        title="Delete">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="px-5 py-16 text-center">
                <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                </svg>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                    @if($filter !== 'all') No {{ $filter }} notifications @else No notifications yet @endif
                </p>
                <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">Alerts from the system will appear here.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($this->notifications->hasPages())
        <div>{{ $this->notifications->links() }}</div>
    @endif

</div>
