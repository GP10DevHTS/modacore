<div x-data="{ open: false }" class="relative" @keydown.escape.window="open = false">

    {{-- Bell button --}}
    <button @click="open = !open"
        class="erp-notif-btn"
        title="Notifications"
        aria-label="Notifications">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($this->unreadCount > 0)
            <span class="erp-notif-count" style="font-size:0.5rem;display:flex;align-items:center;justify-content:center;min-width:0.875rem;height:0.875rem;padding:0 0.2rem;border-radius:9999px;top:0.25rem;right:0.25rem">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @else
            <div class="erp-notif-count" style="display:none"></div>
        @endif
    </button>

    {{-- Dropdown panel --}}
    <div x-show="open"
         x-cloak
         @click.outside="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute right-0 top-full z-50 mt-1.5 w-80 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-700/60 dark:bg-zinc-900">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-zinc-100 px-4 py-3 dark:border-zinc-700/60">
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Notifications</span>
                @if($this->unreadCount > 0)
                    <span class="rounded-full bg-amber-500 px-1.5 py-0.5 text-xs font-bold text-black">
                        {{ $this->unreadCount }}
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($this->unreadCount > 0)
                    <button wire:click="markAllRead"
                        class="text-xs font-medium text-amber-600 hover:text-amber-500 dark:text-amber-400 transition-colors">
                        Mark all read
                    </button>
                @endif
                <a href="{{ route('notifications.index') }}" wire:navigate @click="open = false"
                    class="text-xs text-zinc-400 hover:text-zinc-600 dark:text-zinc-500 dark:hover:text-zinc-300 transition-colors">
                    View all →
                </a>
            </div>
        </div>

        {{-- Notifications list --}}
        <div class="divide-y divide-zinc-100 dark:divide-zinc-700/50 max-h-96 overflow-y-auto">
            @forelse($this->recent as $notification)
                @php
                    $data = $notification->data;
                    $iconCfg = match($data['icon_type'] ?? 'info') {
                        'warning' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-600 dark:text-amber-400'],
                        'danger'  => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-600 dark:text-red-400'],
                        'success' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-600 dark:text-emerald-400'],
                        default   => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-600 dark:text-blue-400'],
                    };
                @endphp
                <div class="group flex items-start gap-3 px-4 py-3 {{ $notification->read_at ? '' : 'bg-amber-50/40 dark:bg-amber-900/5' }} hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors"
                     wire:key="bell-notif-{{ $notification->id }}">

                    {{-- Icon --}}
                    <div class="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-full {{ $iconCfg['bg'] }}">
                        @if(($data['icon_type'] ?? 'info') === 'warning')
                            <svg class="size-3.5 {{ $iconCfg['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        @else
                            <svg class="size-3.5 {{ $iconCfg['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="min-w-0 flex-1">
                        @if(!$notification->read_at)
                            <span class="inline-block mb-0.5 size-1.5 rounded-full bg-amber-500"></span>
                        @endif
                        <p class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 leading-snug">{{ $data['title'] ?? 'Notification' }}</p>
                        <p class="mt-0.5 text-xs leading-snug text-zinc-500 dark:text-zinc-400 line-clamp-2">{{ $data['message'] ?? '' }}</p>
                        <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex shrink-0 flex-col items-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        @if(!$notification->read_at)
                            <button wire:click="markRead('{{ $notification->id }}')"
                                class="rounded p-0.5 text-zinc-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors"
                                title="Mark as read">
                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endif
                        @if(!empty($data['action_url']))
                            <a href="{{ $data['action_url'] }}" wire:navigate @click="open = false"
                                class="rounded p-0.5 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                                title="Go to">
                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-10 text-center">
                    <svg class="mx-auto mb-2 size-8 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    <p class="text-xs font-medium text-zinc-400 dark:text-zinc-500">No notifications</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="border-t border-zinc-100 px-4 py-2.5 dark:border-zinc-700/60">
            <a href="{{ route('notifications.index') }}" wire:navigate @click="open = false"
                class="flex items-center justify-center gap-1 text-xs font-medium text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 transition-colors">
                View all notifications
            </a>
        </div>
    </div>

</div>
