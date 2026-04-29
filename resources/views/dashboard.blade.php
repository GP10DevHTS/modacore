<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-8">

        {{-- Welcome --}}
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                    Welcome back, {{ auth()->user()->name }}
                </h1>
                <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Here's a quick overview of your operations.
                </p>
            </div>
            <a href="{{ route('notifications.index') }}" wire:navigate
                class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-zinc-600 shadow-sm hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-colors">
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                Notifications
            </a>
        </div>

        {{-- Revenue Summary --}}
        @can('reports.view')
            <div>
                <div class="mb-3 flex items-center gap-3">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Revenue Overview</h2>
                    <div class="h-px flex-1 bg-zinc-200 dark:bg-zinc-700/60"></div>
                    <a href="{{ route('reports.index') }}" wire:navigate
                        class="text-xs text-amber-600 hover:underline dark:text-amber-400">Full report →</a>
                </div>
                @livewire('dashboard.revenue-summary')
            </div>
        @endcan

        {{-- Procurement Summary --}}
        @can('inventory.edit')
            <div>
                <div class="mb-3 flex items-center gap-3">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Procurement</h2>
                    <div class="h-px flex-1 bg-zinc-200 dark:bg-zinc-700/60"></div>
                    <a href="{{ route('procurement.dashboard') }}" wire:navigate
                        class="text-xs text-amber-600 hover:underline dark:text-amber-400">Full view →</a>
                </div>
                @livewire('purchase-orders.procurement-dashboard')
            </div>
        @endcan

        {{-- Activity + Audit side-by-side --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
            <div>
                <div class="mb-3 flex items-center gap-3">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Recent Activity</h2>
                    <div class="h-px flex-1 bg-zinc-200 dark:bg-zinc-700/60"></div>
                </div>
                @livewire('dashboard.activity-feed')
            </div>

            @can('inventory.view')
                <div>
                    <div class="mb-3 flex items-center gap-3">
                        <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Audit Log</h2>
                        <div class="h-px flex-1 bg-zinc-200 dark:bg-zinc-700/60"></div>
                        <a href="{{ route('procurement.dashboard') }}" wire:navigate
                            class="text-xs text-amber-600 hover:underline dark:text-amber-400">Procurement →</a>
                    </div>
                    @livewire('dashboard.audit-summary')
                </div>
            @endcan
        </div>

    </div>
</x-layouts::app>
