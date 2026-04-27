<div class="flex min-h-[60vh] flex-col items-center justify-center space-y-6 py-20 text-center">

    <div class="relative flex size-24 items-center justify-center">
        <div class="absolute inset-0 rounded-full bg-amber-400/10 animate-ping"></div>
        <div class="relative flex size-20 items-center justify-center rounded-full bg-amber-50 dark:bg-amber-900/20">
            <svg class="size-10 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    <div class="space-y-2">
        <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Attendance Tracking</h2>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto">
            Clock-in/out, shift management, and attendance reports are coming soon. Stay tuned for updates.
        </p>
    </div>

    <div class="flex flex-wrap items-center justify-center gap-3">
        @foreach(['Clock In / Clock Out', 'Shift Scheduling', 'Leave Requests', 'Attendance Reports'] as $feature)
            <span class="inline-flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-zinc-500 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                <span class="size-1.5 rounded-full bg-amber-400"></span>
                {{ $feature }}
            </span>
        @endforeach
    </div>

    <span class="rounded-full bg-amber-100 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
        Coming Soon
    </span>

</div>
