<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Analytics</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Aggregated metrics and trends across your operations.</p>
        </div>
        <a href="{{ route('reports.index') }}" wire:navigate
            class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Reports
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6">

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/20">
                <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">All Bookings</p>
            <p class="mt-0.5 text-2xl font-bold tabular-nums text-zinc-900 dark:text-zinc-100">{{ number_format($this->totalBookings) }}</p>
        </div>

        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-800/30 dark:bg-emerald-900/10">
            <div class="flex size-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-800/30">
                <svg class="size-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-emerald-700 dark:text-emerald-400">Active</p>
            <p class="mt-0.5 text-2xl font-bold tabular-nums text-emerald-700 dark:text-emerald-300">{{ number_format($this->activeBookings) }}</p>
        </div>

        <div class="rounded-xl border border-violet-200 bg-violet-50 p-5 shadow-sm dark:border-violet-800/30 dark:bg-violet-900/10">
            <div class="flex size-8 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-800/30">
                <svg class="size-4 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-violet-700 dark:text-violet-400">Completed</p>
            <p class="mt-0.5 text-2xl font-bold tabular-nums text-violet-700 dark:text-violet-300">{{ number_format($this->completedBookings) }}</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20">
                <svg class="size-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">Customers</p>
            <p class="mt-0.5 text-2xl font-bold tabular-nums text-zinc-900 dark:text-zinc-100">{{ number_format($this->totalCustomers) }}</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                <svg class="size-4 text-zinc-600 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">Active Items</p>
            <p class="mt-0.5 text-2xl font-bold tabular-nums text-zinc-900 dark:text-zinc-100">{{ number_format($this->activeInventoryItems) }}</p>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-800/30 dark:bg-amber-900/10">
            <div class="flex size-8 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-800/30">
                <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-amber-700 dark:text-amber-400">Total Revenue</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-amber-700 dark:text-amber-300">UGX {{ number_format($this->totalRevenue, 0) }}</p>
        </div>

    </div>

    {{-- Revenue vs Debt --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Booked (Invoiced)</p>
                    <p class="mt-1 text-xl font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->totalBookedValue, 0) }}</p>
                </div>
                <div class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                    <svg class="size-5 text-zinc-500 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-zinc-400">All non-cancelled bookings</p>
        </div>

        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-800/30 dark:bg-emerald-900/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Cash Collected</p>
                    <p class="mt-1 text-xl font-bold tabular-nums text-emerald-700 dark:text-emerald-300">UGX {{ number_format($this->totalCashCollected, 0) }}</p>
                </div>
                <div class="flex size-10 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-800/30">
                    <svg class="size-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-emerald-600/60 dark:text-emerald-500">All payments received</p>
        </div>

        <div class="rounded-xl border border-orange-200 bg-orange-50 p-5 shadow-sm dark:border-orange-800/30 dark:bg-orange-900/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-orange-700 dark:text-orange-400">Customer Debt</p>
                    <p class="mt-1 text-xl font-bold tabular-nums text-orange-700 dark:text-orange-300">UGX {{ number_format($this->totalOutstandingCustomers, 0) }}</p>
                </div>
                <div class="flex size-10 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-800/30">
                    <svg class="size-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs">
                <span class="text-orange-600/70 dark:text-orange-500">Booked but not yet paid</span>
                <a href="{{ route('reports.index') }}" wire:navigate class="text-orange-600 hover:underline dark:text-orange-400">Debtors →</a>
            </div>
        </div>
    </div>

    @can('expenses.view')
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-red-200 bg-red-50 p-5 shadow-sm dark:border-red-800/30 dark:bg-red-900/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-red-700 dark:text-red-400">Total Billed Expenses</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums text-red-700 dark:text-red-300">UGX {{ number_format($this->totalExpensesBilled, 0) }}</p>
                </div>
                <div class="flex size-10 items-center justify-center rounded-xl bg-red-100 dark:bg-red-800/30">
                    <svg class="size-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs">
                <span class="text-red-600/70 dark:text-red-500">All bills recorded</span>
                <a href="{{ route('expenses.index') }}" wire:navigate class="text-red-600 hover:underline dark:text-red-400">View bills →</a>
            </div>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-800/30 dark:bg-amber-900/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-amber-700 dark:text-amber-400">Outstanding Balance</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums text-amber-700 dark:text-amber-300">UGX {{ number_format($this->totalExpensesOutstanding, 0) }}</p>
                </div>
                <div class="flex size-10 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-800/30">
                    <svg class="size-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs">
                <span class="text-amber-600/70 dark:text-amber-500">Unpaid / partially paid</span>
                <a href="{{ route('reports.index') }}" wire:navigate class="text-amber-600 hover:underline dark:text-amber-400">Full report →</a>
            </div>
        </div>
    </div>
    @endcan

    <div class="grid gap-6 lg:grid-cols-2">

        {{-- Revenue by Month --}}
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Revenue Trend</h2>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Last 6 months of rental revenue</p>
            </div>
            @php $maxRev = $this->revenueByMonth->max('total') ?: 1; @endphp
            @forelse($this->revenueByMonth as $row)
                <div class="flex items-center gap-4 border-b border-zinc-100 px-5 py-3.5 last:border-0 dark:border-zinc-700/50"
                     wire:key="rm-{{ $row->month }}">
                    <span class="w-20 shrink-0 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M Y') }}
                    </span>
                    <div class="flex-1">
                        <div class="h-2 w-full rounded-full bg-zinc-100 dark:bg-zinc-700">
                            <div class="h-2 rounded-full bg-amber-500 transition-all"
                                style="width: {{ round(($row->total / $maxRev) * 100) }}%"></div>
                        </div>
                    </div>
                    <div class="w-32 shrink-0 text-right">
                        <span class="text-xs font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">
                            UGX {{ number_format($row->total, 0) }}
                        </span>
                        <span class="ml-1 text-xs text-zinc-400">({{ $row->count }})</span>
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-zinc-400 dark:text-zinc-500">
                    No revenue data available yet.
                </div>
            @endforelse
        </div>

        {{-- Bookings by Status --}}
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Bookings by Status</h2>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Distribution of all bookings</p>
            </div>
            @php
                $totalBks = array_sum($this->bookingsByStatus) ?: 1;
                $bkColors = [
                    'draft'     => ['bar' => 'bg-zinc-400', 'text' => 'text-zinc-600 dark:text-zinc-400'],
                    'confirmed' => ['bar' => 'bg-blue-500', 'text' => 'text-blue-600 dark:text-blue-400'],
                    'active'    => ['bar' => 'bg-emerald-500', 'text' => 'text-emerald-600 dark:text-emerald-400'],
                    'completed' => ['bar' => 'bg-violet-500', 'text' => 'text-violet-600 dark:text-violet-400'],
                    'cancelled' => ['bar' => 'bg-red-500', 'text' => 'text-red-600 dark:text-red-400'],
                ];
            @endphp
            @foreach(['draft', 'confirmed', 'active', 'completed', 'cancelled'] as $status)
                @php
                    $count = $this->bookingsByStatus[$status] ?? 0;
                    $pct = round(($count / $totalBks) * 100);
                    $c = $bkColors[$status];
                @endphp
                <div class="flex items-center gap-4 border-b border-zinc-100 px-5 py-3.5 last:border-0 dark:border-zinc-700/50">
                    <span class="w-20 shrink-0 text-xs font-medium {{ $c['text'] }} capitalize">{{ $status }}</span>
                    <div class="flex-1">
                        <div class="h-2 w-full rounded-full bg-zinc-100 dark:bg-zinc-700">
                            <div class="h-2 rounded-full transition-all {{ $c['bar'] }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    <div class="w-20 shrink-0 text-right">
                        <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($count) }}</span>
                        <span class="ml-1 text-xs text-zinc-400">{{ $pct }}%</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Top Rented Items --}}
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Top Rented Items</h2>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Most booked inventory items</p>
            </div>
            @forelse($this->topItems as $index => $item)
                <div class="flex items-center gap-4 border-b border-zinc-100 px-5 py-3.5 last:border-0 dark:border-zinc-700/50"
                     wire:key="ti-{{ $item->id }}">
                    <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-amber-50 text-xs font-bold text-amber-600 dark:bg-amber-900/20 dark:text-amber-400">
                        {{ $index + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $item->name }}</p>
                        <p class="text-xs text-zinc-400">{{ $item->category?->name ?? 'No category' }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $item->rental_count }}</p>
                        <p class="text-xs text-zinc-400">{{ Str::plural('rental', $item->rental_count) }}</p>
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-zinc-400 dark:text-zinc-500">
                    No rental data available yet.
                </div>
            @endforelse
        </div>

        {{-- Recent Bookings --}}
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                <div>
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Recent Bookings</h2>
                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Latest 5 bookings</p>
                </div>
                <a href="{{ route('bookings.index') }}" wire:navigate class="text-xs text-amber-600 hover:underline dark:text-amber-400">View all →</a>
            </div>
            @forelse($this->recentBookings as $booking)
                @php
                    $bCfg = [
                        'draft'     => ['pill' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400', 'dot' => 'bg-zinc-400'],
                        'confirmed' => ['pill' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400', 'dot' => 'bg-blue-500'],
                        'active'    => ['pill' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400', 'dot' => 'bg-emerald-500'],
                        'completed' => ['pill' => 'bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-400', 'dot' => 'bg-violet-500'],
                        'cancelled' => ['pill' => 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400', 'dot' => 'bg-red-500'],
                    ];
                    $bc = $bCfg[$booking->status] ?? $bCfg['draft'];
                @endphp
                <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-3.5 last:border-0 dark:border-zinc-700/50"
                     wire:key="rb-{{ $booking->id }}">
                    <div>
                        <a href="{{ route('bookings.show', $booking->id) }}" wire:navigate
                            class="font-mono text-sm font-semibold text-amber-600 hover:text-amber-500 dark:text-amber-400">
                            {{ $booking->booking_number }}
                        </a>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">{{ $booking->customer?->name }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold {{ $bc['pill'] }}">
                            <span class="size-1.5 rounded-full {{ $bc['dot'] }}"></span>
                            {{ ucfirst($booking->status) }}
                        </span>
                        <span class="text-sm font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">
                            UGX {{ number_format($booking->total_amount, 0) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-zinc-400 dark:text-zinc-500">No bookings yet.</div>
            @endforelse
        </div>

    </div>

</div>
