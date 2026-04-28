<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Procurement Dashboard</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Financial overview of supplier orders and payments.</p>
        </div>
        @can('inventory.create')
        <a href="{{ route('purchase-orders.create') }}" wire:navigate
            class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-black shadow-sm hover:bg-amber-400 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Order
        </a>
        @endcan
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">

        {{-- Total PO Value --}}
        <div class="col-span-1 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex items-center gap-2">
                <div class="flex size-8 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                    <svg class="size-4 text-zinc-600 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">Total PO Value</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">
                UGX {{ number_format($this->totalPoValue, 0) }}
            </p>
            <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">{{ $this->openOrders }} open order(s)</p>
        </div>

        {{-- Total Invoiced --}}
        <div class="col-span-1 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20">
                <svg class="size-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Invoiced</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">
                UGX {{ number_format($this->totalInvoiced, 0) }}
            </p>
            <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">
                UGX {{ number_format($this->uninvoicedValue, 0) }} uninvoiced
            </p>
        </div>

        {{-- Total Paid --}}
        <div class="col-span-1 rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-800/30 dark:bg-emerald-900/10">
            <div class="flex size-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-800/30">
                <svg class="size-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-emerald-700 dark:text-emerald-400">Total Paid</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-emerald-700 dark:text-emerald-300">
                UGX {{ number_format($this->totalPaid, 0) }}
            </p>
            <p class="mt-1 text-xs text-emerald-600/60 dark:text-emerald-500">Confirmed payments</p>
        </div>

        {{-- Outstanding --}}
        <div class="col-span-1 rounded-xl border border-red-200 bg-red-50 p-5 shadow-sm dark:border-red-800/30 dark:bg-red-900/10">
            <div class="flex size-8 items-center justify-center rounded-lg bg-red-100 dark:bg-red-800/30">
                <svg class="size-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-red-700 dark:text-red-400">Outstanding</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-red-700 dark:text-red-300">
                UGX {{ number_format($this->totalOutstanding, 0) }}
            </p>
            <p class="mt-1 text-xs text-red-600/60 dark:text-red-500">Invoiced but unpaid</p>
        </div>

        {{-- Orders by Status --}}
        <div class="col-span-1 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/20">
                <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">Orders by Status</p>
            <div class="mt-1 space-y-0.5 text-xs">
                @foreach([
                    ['status' => 'draft', 'label' => 'Draft', 'color' => 'text-zinc-500'],
                    ['status' => 'approved', 'label' => 'Approved', 'color' => 'text-violet-600'],
                    ['status' => 'sent', 'label' => 'Sent', 'color' => 'text-blue-600'],
                    ['status' => 'cancelled', 'label' => 'Cancelled', 'color' => 'text-red-500'],
                ] as $s)
                <div class="flex justify-between">
                    <span class="{{ $s['color'] }}">{{ $s['label'] }}</span>
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $this->ordersByStatus[$s['status']] ?? 0 }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Overdue Invoices --}}
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Overdue Invoices</h2>
                @if($this->overdueInvoices->isNotEmpty())
                    <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400">
                        {{ $this->overdueInvoices->count() }} overdue
                    </span>
                @endif
            </div>
            @forelse($this->overdueInvoices as $inv)
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-zinc-100 last:border-0 dark:border-zinc-700/50">
                <div>
                    <a href="{{ route('purchase-orders.show', $inv->purchase_order_id) }}" wire:navigate
                        class="font-mono text-sm font-semibold text-amber-600 hover:text-amber-500 dark:text-amber-400">
                        {{ $inv->invoice_number }}
                    </a>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $inv->purchaseOrder->supplier->name }}
                        &middot; Due {{ $inv->due_date->format('d M Y') }}
                        <span class="text-red-500">({{ $inv->due_date->diffForHumans() }})</span>
                    </p>
                </div>
                <div class="text-right">
                    <p class="font-bold tabular-nums text-red-600 dark:text-red-400">UGX {{ number_format($inv->outstandingBalance(), 0) }}</p>
                    @can('inventory.edit')
                    <a href="{{ route('purchase-orders.payment', $inv->id) }}" wire:navigate
                        class="text-xs text-amber-600 hover:underline dark:text-amber-400">Pay now →</a>
                    @endcan
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-sm text-zinc-400 dark:text-zinc-500">
                <svg class="mx-auto mb-2 size-8 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                No overdue invoices
            </div>
            @endforelse
        </div>

        {{-- Recent Orders --}}
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Recent Orders</h2>
                <a href="{{ route('purchase-orders.index') }}" wire:navigate class="text-xs text-amber-600 hover:underline dark:text-amber-400">View all →</a>
            </div>
            @forelse($this->recentOrders as $order)
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-zinc-100 last:border-0 dark:border-zinc-700/50">
                <div>
                    <a href="{{ route('purchase-orders.show', $order->id) }}" wire:navigate
                        class="font-mono text-sm font-semibold text-amber-600 hover:text-amber-500 dark:text-amber-400">
                        {{ $order->po_number }}
                    </a>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $order->supplier->name }} &middot; {{ $order->created_at->format('d M Y') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold {{ $order->order_status->pillClasses() }}">
                        <span class="size-1.5 rounded-full {{ $order->order_status->dotClasses() }}"></span>
                        {{ $order->order_status->label() }}
                    </span>
                    <span class="text-sm font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">
                        UGX {{ number_format($order->total_amount, 0) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-sm text-zinc-400 dark:text-zinc-500">No orders yet.</div>
            @endforelse
        </div>

    </div>

</div>
