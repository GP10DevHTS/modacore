<div class="space-y-5">

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6">

        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-800/30 dark:bg-emerald-900/10">
            <div class="flex size-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-800/30">
                <svg class="size-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-emerald-700 dark:text-emerald-400">Total Revenue</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-emerald-700 dark:text-emerald-300">UGX {{ number_format($this->totalRevenue, 0) }}</p>
            <p class="mt-1 text-xs text-emerald-600/60 dark:text-emerald-500">All rental payments</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/20">
                <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">This Month</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->monthRevenue, 0) }}</p>
            <p class="mt-1 text-xs text-zinc-400">{{ now()->format('F Y') }}</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-violet-50 dark:bg-violet-900/20">
                <svg class="size-4 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">Today</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->todayRevenue, 0) }}</p>
            <p class="mt-1 text-xs text-zinc-400">{{ now()->format('d M Y') }}</p>
        </div>

        <div class="rounded-xl border border-blue-200 bg-blue-50 p-5 shadow-sm dark:border-blue-800/30 dark:bg-blue-900/10">
            <div class="flex size-8 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-800/30">
                <svg class="size-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-blue-700 dark:text-blue-400">Deposits Held</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-blue-700 dark:text-blue-300">UGX {{ number_format($this->depositsHeld, 0) }}</p>
            <p class="mt-1 text-xs text-blue-600/60 dark:text-blue-500">Security deposits</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                <svg class="size-4 text-zinc-600 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">Active Value</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->activeBookingsValue, 0) }}</p>
            <p class="mt-1 text-xs text-zinc-400">In-flight bookings</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                <svg class="size-4 text-zinc-600 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">Avg. Booking</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->avgBookingValue, 0) }}</p>
            <p class="mt-1 text-xs text-zinc-400">Per booking</p>
        </div>

    </div>

    {{-- Revenue trend --}}
    @if($this->revenueByMonth->isNotEmpty())
    <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Revenue Trend (Last 6 Months)</h3>
            <a href="{{ route('reports.index') }}" wire:navigate class="text-xs text-amber-600 hover:underline dark:text-amber-400">Full report →</a>
        </div>
        @php $maxRev = $this->revenueByMonth->max('total') ?: 1; @endphp
        <div class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
            @foreach($this->revenueByMonth as $row)
                <div class="flex items-center gap-4 px-5 py-3" wire:key="rev-{{ $row->month }}">
                    <span class="w-20 shrink-0 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M Y') }}
                    </span>
                    <div class="flex-1">
                        <div class="h-2 w-full rounded-full bg-zinc-100 dark:bg-zinc-700">
                            <div class="h-2 rounded-full bg-amber-500" style="width: {{ round(($row->total / $maxRev) * 100) }}%"></div>
                        </div>
                    </div>
                    <div class="w-36 shrink-0 text-right">
                        <span class="text-xs font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($row->total, 0) }}</span>
                        <span class="ml-1 text-xs text-zinc-400">({{ $row->cnt }})</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
