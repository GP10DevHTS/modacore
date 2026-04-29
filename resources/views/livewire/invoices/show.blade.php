<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('invoices.index') }}" wire:navigate
            class="flex size-8 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 hover:bg-zinc-50 hover:text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                {{ $this->detail->invoice_number }}
            </h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                Supplier invoice from {{ $this->detail->purchaseOrder?->supplier?->name ?? '—' }}
            </p>
        </div>
        <div class="ml-auto">
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-semibold {{ $this->detail->payment_status->pillClasses() }}">
                <span class="size-1.5 rounded-full {{ $this->detail->payment_status->dotClasses() }}"></span>
                {{ $this->detail->payment_status->label() }}
            </span>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Left column: Invoice details + line items --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Invoice Info Card --}}
            <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Invoice Details</h2>
                </div>
                <div class="grid grid-cols-2 gap-4 p-5 sm:grid-cols-3">
                    <div>
                        <p class="text-xs font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Invoice #</p>
                        <p class="mt-1 font-mono text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $this->detail->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Supplier Ref</p>
                        <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $this->detail->supplier_invoice_ref ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Purchase Order</p>
                        @if($this->detail->purchaseOrder)
                            <a href="{{ route('purchase-orders.show', $this->detail->purchase_order_id) }}" wire:navigate
                                class="mt-1 block font-mono text-sm font-semibold text-amber-600 hover:text-amber-500 dark:text-amber-400">
                                {{ $this->detail->purchaseOrder->po_number }}
                            </a>
                        @else
                            <p class="mt-1 text-sm text-zinc-400">—</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Supplier</p>
                        <p class="mt-1 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->detail->purchaseOrder?->supplier?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Invoice Date</p>
                        <p class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $this->detail->invoice_date?->format('d M Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Due Date</p>
                        <p class="mt-1 text-sm {{ $this->detail->due_date?->isPast() && $this->detail->payment_status !== \App\Enums\PaymentStatus::FullyPaid ? 'font-semibold text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                            {{ $this->detail->due_date?->format('d M Y') ?? '—' }}
                        </p>
                    </div>
                    @if($this->detail->notes)
                    <div class="col-span-2 sm:col-span-3">
                        <p class="text-xs font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Notes</p>
                        <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-300">{{ $this->detail->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Line Items --}}
            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Line Items</h2>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-100 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Item</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Qty</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Unit Cost</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                        @forelse($this->detail->items as $item)
                            <tr wire:key="item-{{ $item->id }}">
                                <td class="px-5 py-3 text-zinc-800 dark:text-zinc-200">
                                    {{ $item->inventoryItem?->name ?? 'Unknown item' }}
                                </td>
                                <td class="px-5 py-3 text-right tabular-nums text-zinc-700 dark:text-zinc-300">{{ $item->quantity }}</td>
                                <td class="px-5 py-3 text-right tabular-nums text-zinc-700 dark:text-zinc-300">UGX {{ number_format($item->unit_cost, 0) }}</td>
                                <td class="px-5 py-3 text-right tabular-nums font-semibold text-zinc-900 dark:text-zinc-100">UGX {{ number_format($item->subtotal, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-sm text-zinc-400">No line items recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                            <td colspan="3" class="px-5 py-3 text-right text-sm font-semibold text-zinc-700 dark:text-zinc-300">Total</td>
                            <td class="px-5 py-3 text-right text-sm font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->detail->total_amount, 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>

        {{-- Right column: Payment summary --}}
        <div class="space-y-6">

            {{-- Balance Summary --}}
            <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Balance</h2>
                </div>
                <div class="space-y-3 p-5">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400">Invoice Total</span>
                        <span class="font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->detail->total_amount, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400">Total Paid</span>
                        <span class="font-semibold tabular-nums text-emerald-600 dark:text-emerald-400">UGX {{ number_format($this->detail->totalPaid(), 0) }}</span>
                    </div>
                    <div class="border-t border-zinc-100 pt-3 dark:border-zinc-700/50">
                        <div class="flex justify-between">
                            <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Outstanding</span>
                            <span class="text-base font-bold tabular-nums {{ $this->detail->outstandingBalance() > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                UGX {{ number_format($this->detail->outstandingBalance(), 0) }}
                            </span>
                        </div>
                    </div>
                </div>
                @can('inventory.edit')
                    @if($this->detail->outstandingBalance() > 0)
                        <div class="border-t border-zinc-100 px-5 py-3 dark:border-zinc-700/50">
                            <a href="{{ route('purchase-orders.payment', $this->detail->id) }}" wire:navigate
                                class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-amber-500 px-3 py-2 text-sm font-semibold text-black hover:bg-amber-400 transition-colors">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                Record Payment
                            </a>
                        </div>
                    @endif
                @endcan
            </div>

            {{-- Payment History --}}
            <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Payment History</h2>
                </div>
                @forelse($this->detail->payments as $payment)
                    <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-3.5 last:border-0 dark:border-zinc-700/50" wire:key="pay-{{ $payment->id }}">
                        <div>
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $payment->payment_reference ?? $payment->payment_method }}</p>
                            <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $payment->payment_date?->format('d M Y') ?? '—' }} &middot; {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                        </div>
                        <span class="text-sm font-semibold tabular-nums text-emerald-600 dark:text-emerald-400">
                            UGX {{ number_format($payment->amount, 0) }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <p class="text-sm text-zinc-400 dark:text-zinc-500">No payments recorded yet.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

</div>
