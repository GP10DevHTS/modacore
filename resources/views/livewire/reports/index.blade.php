<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Reports</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Financial and operational summaries for your business.</p>
        </div>
        <a href="{{ route('analytics.dashboard') }}" wire:navigate
            class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
            </svg>
            Analytics
        </a>
    </div>

    {{-- KPI Summary Cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-7">

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/20">
                <svg class="size-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Revenue</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->totalRevenue, 0) }}</p>
            <p class="mt-1 text-xs text-zinc-400">Rental payments</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20">
                <svg class="size-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">Deposits Held</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->totalDeposits, 0) }}</p>
            <p class="mt-1 text-xs text-zinc-400">Security deposits</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-violet-50 dark:bg-violet-900/20">
                <svg class="size-4 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Bookings</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">{{ number_format($this->totalBookings) }}</p>
            <p class="mt-1 text-xs text-zinc-400">All time</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                <svg class="size-4 text-zinc-600 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">Supplier Invoiced</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->totalSupplierInvoiced, 0) }}</p>
            <p class="mt-1 text-xs text-zinc-400">All supplier invoices</p>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-800/30 dark:bg-amber-900/10">
            <div class="flex size-8 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-800/30">
                <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-amber-700 dark:text-amber-400">Supplier Paid</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-amber-700 dark:text-amber-300">UGX {{ number_format($this->totalSupplierPaid, 0) }}</p>
            <p class="mt-1 text-xs text-amber-600/70 dark:text-amber-500">Payments made</p>
        </div>

        @can('expenses.view')
        <div class="rounded-xl border border-red-200 bg-white p-5 shadow-sm dark:border-red-800/30 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-red-50 dark:bg-red-900/20">
                <svg class="size-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Expenses</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-red-600 dark:text-red-400">UGX {{ number_format($this->totalExpenses, 0) }}</p>
            <p class="mt-1 text-xs text-zinc-400">Approved only</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg {{ $this->netPosition >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                <svg class="size-4 {{ $this->netPosition >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">Net Position</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums {{ $this->netPosition >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                {{ $this->netPosition >= 0 ? '' : '-' }}UGX {{ number_format(abs($this->netPosition), 0) }}
            </p>
            <p class="mt-1 text-xs text-zinc-400">Revenue − Expenses</p>
        </div>
        @endcan

    </div>

    {{-- Report Tabs --}}
    <div class="flex gap-1 border-b border-zinc-200 dark:border-zinc-700/60">
        <button wire:click="$set('activeReport', 'payments')"
            class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px
                   {{ $activeReport === 'payments' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
            Payments by Month
        </button>
        <button wire:click="$set('activeReport', 'bookings')"
            class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px
                   {{ $activeReport === 'bookings' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
            Bookings by Status
        </button>
        <button wire:click="$set('activeReport', 'recent')"
            class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px
                   {{ $activeReport === 'recent' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
            Recent Payments
        </button>
        @can('expenses.view')
        <button wire:click="$set('activeReport', 'expenses-month')"
            class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px
                   {{ $activeReport === 'expenses-month' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
            Expenses by Month
        </button>
        <button wire:click="$set('activeReport', 'expenses-category')"
            class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px
                   {{ $activeReport === 'expenses-category' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
            Expenses by Category
        </button>
        <button wire:click="$set('activeReport', 'financial-summary')"
            class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px
                   {{ $activeReport === 'financial-summary' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
            Financial Summary
        </button>
        @endcan
    </div>

    {{-- Payments by Month --}}
    @if($activeReport === 'payments')
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Monthly Payment Summary</h2>
            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Last 12 months of customer payments (excluding deposits)</p>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-100 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Month</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Transactions</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Collected</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Bar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @php $maxAmount = $this->paymentsByMonth->max('total') ?: 1; @endphp
                @forelse($this->paymentsByMonth as $row)
                    <tr wire:key="pm-{{ $row->month }}" class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-5 py-3.5 font-medium text-zinc-900 dark:text-zinc-100">
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('F Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-right tabular-nums text-zinc-600 dark:text-zinc-400">{{ number_format($row->count) }}</td>
                        <td class="px-5 py-3.5 text-right tabular-nums font-semibold text-zinc-900 dark:text-zinc-100">
                            UGX {{ number_format($row->total, 0) }}
                        </td>
                        <td class="px-5 py-3.5 text-right w-32">
                            <div class="flex items-center justify-end gap-2">
                                <div class="h-2 w-24 rounded-full bg-zinc-100 dark:bg-zinc-700">
                                    <div class="h-2 rounded-full bg-amber-500"
                                        style="width: {{ round(($row->total / $maxAmount) * 100) }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-sm text-zinc-400">No payment data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    {{-- Bookings by Status --}}
    @if($activeReport === 'bookings')
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Bookings by Status</h2>
            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Count and total value grouped by booking status</p>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-100 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Count</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @php
                    $statusCfg = [
                        'draft'     => ['pill' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400', 'dot' => 'bg-zinc-400'],
                        'confirmed' => ['pill' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400', 'dot' => 'bg-blue-500'],
                        'active'    => ['pill' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400', 'dot' => 'bg-emerald-500'],
                        'completed' => ['pill' => 'bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-400', 'dot' => 'bg-violet-500'],
                        'cancelled' => ['pill' => 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400', 'dot' => 'bg-red-500'],
                    ];
                @endphp
                @forelse($this->bookingsByStatus as $row)
                    @php $cfg = $statusCfg[$row->status] ?? $statusCfg['draft']; @endphp
                    <tr wire:key="bs-{{ $row->status }}" class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $cfg['pill'] }}">
                                <span class="size-1.5 rounded-full {{ $cfg['dot'] }}"></span>
                                {{ ucfirst($row->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right tabular-nums font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($row->count) }}</td>
                        <td class="px-5 py-3.5 text-right tabular-nums font-semibold text-zinc-900 dark:text-zinc-100">UGX {{ number_format($row->total, 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-12 text-center text-sm text-zinc-400">No booking data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    {{-- Expenses by Month --}}
    @if($activeReport === 'expenses-month')
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Expenses by Month</h2>
            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Approved vs planned expenses grouped by month</p>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-100 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Month</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Approved</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Planned (Draft)</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @forelse($this->expensesByMonth as $month => $rows)
                    @php
                        $approved = $rows->where('status', 'approved')->sum('total');
                        $draft    = $rows->where('status', 'draft')->sum('total');
                    @endphp
                    <tr wire:key="em-{{ $month }}" class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-5 py-3.5 font-medium text-zinc-900 dark:text-zinc-100">
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-right tabular-nums text-red-600 dark:text-red-400 font-semibold">
                            UGX {{ number_format($approved, 0) }}
                        </td>
                        <td class="px-5 py-3.5 text-right tabular-nums text-amber-600 dark:text-amber-400">
                            UGX {{ number_format($draft, 0) }}
                        </td>
                        <td class="px-5 py-3.5 text-right tabular-nums font-bold text-zinc-900 dark:text-zinc-100">
                            UGX {{ number_format($approved + $draft, 0) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-sm text-zinc-400">No expense data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    {{-- Expenses by Category --}}
    @if($activeReport === 'expenses-category')
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Expenses by Category</h2>
            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Approved spend grouped by expense category</p>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-100 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Category</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Records</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Approved Total</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Share</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @php $grandTotal = $this->expensesByCategory->sum('expenses_sum_amount') ?: 1; @endphp
                @forelse($this->expensesByCategory as $cat)
                    <tr wire:key="ec-{{ $cat->id }}" class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-5 py-3.5 font-medium text-zinc-900 dark:text-zinc-100">{{ $cat->name }}</td>
                        <td class="px-5 py-3.5 text-right tabular-nums text-zinc-600 dark:text-zinc-400">{{ number_format($cat->expenses_count) }}</td>
                        <td class="px-5 py-3.5 text-right tabular-nums font-semibold text-red-600 dark:text-red-400">
                            UGX {{ number_format($cat->expenses_sum_amount ?? 0, 0) }}
                        </td>
                        <td class="px-5 py-3.5 text-right w-36">
                            <div class="flex items-center justify-end gap-2">
                                <span class="text-xs tabular-nums text-zinc-500 dark:text-zinc-400 w-10 text-right">
                                    {{ number_format(($cat->expenses_sum_amount / $grandTotal) * 100, 1) }}%
                                </span>
                                <div class="h-2 w-20 rounded-full bg-zinc-100 dark:bg-zinc-700">
                                    <div class="h-2 rounded-full bg-red-500"
                                        style="width: {{ round(($cat->expenses_sum_amount / $grandTotal) * 100) }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-sm text-zinc-400">No categories or approved expenses yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    {{-- Financial Summary --}}
    @if($activeReport === 'financial-summary')
    <div class="space-y-4">
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Financial Summary</h2>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Overall financial position: revenue vs expenses</p>
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                <div class="flex items-center justify-between px-5 py-4">
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Total Revenue</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">All rental payments collected</p>
                    </div>
                    <p class="text-base font-bold tabular-nums text-emerald-600 dark:text-emerald-400">
                        UGX {{ number_format($this->totalRevenue, 0) }}
                    </p>
                </div>
                <div class="flex items-center justify-between px-5 py-4">
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Total Expenses (Approved)</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Confirmed spend across all categories</p>
                    </div>
                    <p class="text-base font-bold tabular-nums text-red-600 dark:text-red-400">
                        − UGX {{ number_format($this->totalExpenses, 0) }}
                    </p>
                </div>
                <div class="flex items-center justify-between px-5 py-4 {{ $this->netPosition >= 0 ? 'bg-emerald-50/40 dark:bg-emerald-900/10' : 'bg-red-50/40 dark:bg-red-900/10' }}">
                    <div>
                        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Net Position</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Revenue minus approved expenses</p>
                    </div>
                    <p class="text-xl font-bold tabular-nums {{ $this->netPosition >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $this->netPosition >= 0 ? '' : '−' }} UGX {{ number_format(abs($this->netPosition), 0) }}
                    </p>
                </div>
                <div class="flex items-center justify-between px-5 py-4 bg-zinc-50/60 dark:bg-zinc-800/30">
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Planned Expenses (Draft)</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Pending approval — not yet deducted</p>
                    </div>
                    <p class="text-sm tabular-nums text-amber-600 dark:text-amber-400">
                        UGX {{ number_format($this->totalPlannedExpenses, 0) }}
                    </p>
                </div>
                <div class="flex items-center justify-between px-5 py-4 bg-zinc-50/60 dark:bg-zinc-800/30">
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Worst-Case Net (incl. planned)</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">If all draft expenses are approved</p>
                    </div>
                    @php $worstCase = $this->netPosition - $this->totalPlannedExpenses; @endphp
                    <p class="text-sm tabular-nums {{ $worstCase >= 0 ? 'text-zinc-600 dark:text-zinc-400' : 'text-red-500 dark:text-red-400' }}">
                        {{ $worstCase >= 0 ? '' : '−' }} UGX {{ number_format(abs($worstCase), 0) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Recent Payments --}}
    @if($activeReport === 'recent')
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Recent Payments</h2>
            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Latest 15 customer payments across all bookings</p>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-100 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Receipt #</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Customer</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Booking</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Method</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Date</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @forelse($this->recentPayments as $payment)
                    <tr wire:key="rp-{{ $payment->id }}" class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-xs font-semibold text-amber-600 dark:text-amber-400">
                                {{ $payment->receipt_number ?? '—' }}
                            </span>
                            @if($payment->is_deposit)
                                <span class="ml-1.5 rounded-full bg-blue-50 px-1.5 py-0.5 text-xs font-medium text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">Deposit</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $payment->booking?->customer?->name ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if($payment->booking)
                                <a href="{{ route('bookings.show', $payment->booking_id) }}" wire:navigate
                                    class="font-mono text-xs text-zinc-600 hover:text-amber-600 dark:text-zinc-400 dark:hover:text-amber-400 transition-colors">
                                    {{ $payment->booking->booking_number }}
                                </a>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-zinc-600 dark:text-zinc-400">
                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                        </td>
                        <td class="px-5 py-3.5 text-zinc-600 dark:text-zinc-400">
                            {{ $payment->paid_at?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5 text-right tabular-nums font-semibold text-zinc-900 dark:text-zinc-100">
                            UGX {{ number_format($payment->amount, 0) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-sm text-zinc-400">No payments recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

</div>
