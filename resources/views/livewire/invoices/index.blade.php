<div class="space-y-5">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Supplier Invoices</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">All invoices received from suppliers against purchase orders.</p>
        </div>
        <a href="{{ route('procurement.dashboard') }}" wire:navigate
            class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4"/>
            </svg>
            Procurement
        </a>
    </div>

    {{-- Table Card --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3 border-b border-zinc-200 bg-zinc-50/60 px-5 py-3 dark:border-zinc-700/60 dark:bg-zinc-800/40">
            <div class="relative w-72">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search invoice number or supplier…"
                    class="w-full rounded-lg border border-zinc-200 bg-white py-2 pl-9 pr-3 text-sm text-zinc-900 placeholder-zinc-400 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder-zinc-500"
                />
            </div>
            <div class="flex gap-1.5">
                @foreach([
                    '' => ['label' => 'All', 'class' => 'bg-zinc-100 text-zinc-700 ring-zinc-200 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-700'],
                    'unpaid' => ['label' => 'Unpaid', 'class' => 'bg-red-50 text-red-700 ring-red-100 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:ring-red-800/30'],
                    'partially_paid' => ['label' => 'Partial', 'class' => 'bg-amber-50 text-amber-700 ring-amber-100 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:ring-amber-800/30'],
                    'fully_paid' => ['label' => 'Paid', 'class' => 'bg-emerald-50 text-emerald-700 ring-emerald-100 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:ring-emerald-800/30'],
                ] as $value => $cfg)
                    <button wire:click="$set('statusFilter', '{{ $value }}')"
                        class="rounded-md px-2.5 py-1 text-xs transition-colors {{ $cfg['class'] }} {{ $statusFilter === $value ? 'ring-2 font-semibold' : 'ring-1' }}">
                        {{ $cfg['label'] }}
                    </button>
                @endforeach
            </div>
            <span class="ml-auto text-xs font-medium text-zinc-400 dark:text-zinc-500">
                {{ $this->invoices->total() }} {{ Str::plural('invoice', $this->invoices->total()) }}
            </span>
        </div>

        {{-- Table --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Invoice #</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Supplier</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">PO Reference</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Invoice Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Due Date</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Amount</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @forelse($this->invoices as $invoice)
                    <tr class="group transition-colors hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30" wire:key="invoice-{{ $invoice->id }}">

                        <td class="px-5 py-3.5">
                            <a href="{{ route('invoices.show', $invoice->id) }}" wire:navigate
                                class="font-mono text-sm font-semibold text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300 transition-colors">
                                {{ $invoice->invoice_number }}
                            </a>
                            @if($invoice->supplier_invoice_ref)
                                <div class="text-xs text-zinc-400 mt-0.5">Ref: {{ $invoice->supplier_invoice_ref }}</div>
                            @endif
                        </td>

                        <td class="px-5 py-3.5">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $invoice->purchaseOrder?->supplier?->name ?? '—' }}
                            </div>
                        </td>

                        <td class="px-5 py-3.5">
                            @if($invoice->purchaseOrder)
                                <a href="{{ route('purchase-orders.show', $invoice->purchase_order_id) }}" wire:navigate
                                    class="font-mono text-xs text-zinc-600 hover:text-amber-600 dark:text-zinc-400 dark:hover:text-amber-400 transition-colors">
                                    {{ $invoice->purchaseOrder->po_number }}
                                </a>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>

                        <td class="px-5 py-3.5 text-zinc-600 dark:text-zinc-400">
                            {{ $invoice->invoice_date?->format('d M Y') ?? '—' }}
                        </td>

                        <td class="px-5 py-3.5">
                            @if($invoice->due_date)
                                <span class="{{ $invoice->due_date->isPast() && $invoice->payment_status !== \App\Enums\PaymentStatus::FullyPaid ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400' }}">
                                    {{ $invoice->due_date->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>

                        <td class="px-5 py-3.5 text-right font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">
                            UGX {{ number_format($invoice->total_amount, 0) }}
                        </td>

                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $invoice->payment_status->pillClasses() }}">
                                <span class="size-1.5 rounded-full {{ $invoice->payment_status->dotClasses() }}"></span>
                                {{ $invoice->payment_status->label() }}
                            </span>
                        </td>

                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('invoices.show', $invoice->id) }}" wire:navigate
                                class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors inline-flex"
                                title="View invoice">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-16 text-center">
                            <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                            </svg>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                @if($search || $statusFilter) No invoices match your filters @else No supplier invoices yet @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($this->invoices->hasPages())
            <div class="border-t border-zinc-200 px-5 py-3 dark:border-zinc-700/60">
                {{ $this->invoices->links() }}
            </div>
        @endif

    </div>

</div>
