<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('purchase-orders.index') }}" wire:navigate
                class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 shadow-sm hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="font-mono text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->order->po_number }}</h1>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $this->order->order_status->pillClasses() }}">
                        <span class="size-1.5 rounded-full {{ $this->order->order_status->dotClasses() }}"></span>
                        {{ $this->order->order_status->label() }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $this->order->closure_status->pillClasses() }}">
                        {{ $this->order->closure_status->label() }}
                    </span>
                </div>
                <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $this->order->supplier->name }} &middot; Created {{ $this->order->created_at->format('d M Y') }}
                    @if($this->order->createdBy) by {{ $this->order->createdBy->name }} @endif
                </p>
            </div>
        </div>

        {{-- Action buttons --}}
        @can('inventory.edit')
        <div class="flex flex-wrap gap-2">
            @if($this->order->order_status === \App\Enums\OrderStatus::Draft)
                @if($this->order->order_status->canEdit())
                    <a href="{{ route('purchase-orders.edit', $this->order->id) }}" wire:navigate>
                        <flux:button variant="ghost" icon="pencil" size="sm">Edit</flux:button>
                    </a>
                @endif
                <flux:button wire:click="approvePo" variant="filled" icon="check" size="sm">Approve</flux:button>
            @endif

            @if($this->order->order_status === \App\Enums\OrderStatus::Approved)
                <a href="{{ route('purchase-orders.edit', $this->order->id) }}" wire:navigate>
                    <flux:button variant="ghost" icon="pencil" size="sm">Edit</flux:button>
                </a>
                <flux:button wire:click="sendPo" variant="primary" icon="paper-airplane" size="sm">Send to Supplier</flux:button>
            @endif

            @if($this->order->order_status === \App\Enums\OrderStatus::Sent)
                <a href="{{ route('purchase-orders.receive-goods', $this->order->id) }}" wire:navigate>
                    <flux:button variant="filled" icon="inbox-arrow-down" size="sm">Receive Goods</flux:button>
                </a>
            @endif

            @if($this->order->order_status !== \App\Enums\OrderStatus::Cancelled)
                <a href="{{ route('purchase-orders.invoice', $this->order->id) }}" wire:navigate>
                    <flux:button variant="ghost" icon="document-text" size="sm">Add Invoice</flux:button>
                </a>
            @endif
        </div>
        @endcan
    </div>

    {{-- Status Strip --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach([
            ['label' => 'Receipt', 'status' => $this->order->receipt_status->label(), 'pill' => $this->order->receipt_status->pillClasses(), 'dot' => $this->order->receipt_status->dotClasses()],
            ['label' => 'Invoice', 'status' => $this->order->invoice_status->label(), 'pill' => $this->order->invoice_status->pillClasses(), 'dot' => $this->order->invoice_status->dotClasses()],
            ['label' => 'Payment', 'status' => $this->order->payment_status->label(), 'pill' => $this->order->payment_status->pillClasses(), 'dot' => $this->order->payment_status->dotClasses()],
        ] as $s)
        <div class="rounded-xl border border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700/60 dark:bg-zinc-900">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $s['label'] }}</p>
            <span class="mt-1 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $s['pill'] }}">
                <span class="size-1.5 rounded-full {{ $s['dot'] }}"></span>
                {{ $s['status'] }}
            </span>
        </div>
        @endforeach
        <div class="rounded-xl border border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700/60 dark:bg-zinc-900">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Outstanding</p>
            <p class="mt-1 text-base font-bold tabular-nums text-red-600 dark:text-red-400">
                UGX {{ number_format($this->order->outstandingBalance(), 0) }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Left: Main content --}}
        <div class="space-y-5 lg:col-span-2">

            {{-- Order Items --}}
            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Order Items</h2>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-100 bg-zinc-50/60 dark:border-zinc-700/50 dark:bg-zinc-800/40">
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Item</th>
                            <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500">Ordered</th>
                            <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500">Received</th>
                            <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500">Invoiced</th>
                            <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500">Unit Cost</th>
                            <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                        @foreach($this->order->items as $item)
                        <tr>
                            <td class="px-5 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $item->inventoryItem->name }}</td>
                            <td class="px-5 py-3 text-right tabular-nums text-zinc-600 dark:text-zinc-400">{{ $item->quantity }}</td>
                            <td class="px-5 py-3 text-right tabular-nums {{ $item->received_quantity < $item->quantity ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                {{ $item->received_quantity }}
                            </td>
                            <td class="px-5 py-3 text-right tabular-nums {{ $item->invoiced_quantity < $item->quantity ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                {{ $item->invoiced_quantity }}
                            </td>
                            <td class="px-5 py-3 text-right tabular-nums text-zinc-600 dark:text-zinc-400">UGX {{ number_format($item->unit_cost, 0) }}</td>
                            <td class="px-5 py-3 text-right font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($item->subtotal, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-zinc-200 bg-zinc-50/60 dark:border-zinc-700/60 dark:bg-zinc-800/40">
                            <td colspan="5" class="px-5 py-3 text-right text-sm font-semibold text-zinc-900 dark:text-zinc-100">Total</td>
                            <td class="px-5 py-3 text-right font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->order->total_amount, 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Goods Receipts --}}
            @if($this->order->goodsReceipts->isNotEmpty())
            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Goods Receipts (GRN)</h2>
                </div>
                @foreach($this->order->goodsReceipts as $grn)
                <div class="border-b border-zinc-100 px-5 py-4 last:border-0 dark:border-zinc-700/50">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-mono text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $grn->grn_number }}</span>
                            <span class="ml-2 text-xs text-zinc-500 dark:text-zinc-400">{{ $grn->received_date->format('d M Y') }}</span>
                            @if($grn->receivedBy)
                                <span class="ml-2 text-xs text-zinc-400 dark:text-zinc-500">by {{ $grn->receivedBy->name }}</span>
                            @endif
                        </div>
                        <span class="text-xs text-zinc-400">{{ $grn->items->count() }} {{ Str::plural('line', $grn->items->count()) }}</span>
                    </div>
                    @if($grn->notes)
                        <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">{{ $grn->notes }}</p>
                    @endif
                    <div class="mt-2 space-y-1">
                        @foreach($grn->items as $grnItem)
                        <div class="flex items-center justify-between text-xs text-zinc-600 dark:text-zinc-400">
                            <span>{{ $grnItem->inventoryItem->name }}</span>
                            <span class="tabular-nums font-medium">× {{ $grnItem->quantity_received }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Invoices --}}
            @if($this->order->supplierInvoices->isNotEmpty())
            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Supplier Invoices</h2>
                </div>
                @foreach($this->order->supplierInvoices as $invoice)
                <div class="border-b border-zinc-100 px-5 py-4 last:border-0 dark:border-zinc-700/50">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <span class="font-mono text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $invoice->invoice_number }}</span>
                            @if($invoice->supplier_invoice_ref)
                                <span class="ml-2 text-xs text-zinc-400">(Ref: {{ $invoice->supplier_invoice_ref }})</span>
                            @endif
                            <span class="ml-2 text-xs text-zinc-500 dark:text-zinc-400">{{ $invoice->invoice_date->format('d M Y') }}</span>
                            @if($invoice->due_date)
                                <span class="ml-2 text-xs {{ $invoice->due_date->isPast() && $invoice->payment_status !== \App\Enums\PaymentStatus::FullyPaid ? 'text-red-500' : 'text-zinc-400' }}">
                                    Due {{ $invoice->due_date->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $invoice->payment_status->pillClasses() }}">
                                <span class="size-1.5 rounded-full {{ $invoice->payment_status->dotClasses() }}"></span>
                                {{ $invoice->payment_status->label() }}
                            </span>
                            <span class="font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($invoice->total_amount, 0) }}</span>
                            @can('inventory.edit')
                            @if($invoice->payment_status !== \App\Enums\PaymentStatus::FullyPaid)
                                <a href="{{ route('purchase-orders.payment', $invoice->id) }}" wire:navigate>
                                    <flux:button variant="ghost" icon="banknotes" size="xs">Pay</flux:button>
                                </a>
                            @endif
                            @endcan
                        </div>
                    </div>
                    {{-- Payments under this invoice --}}
                    @foreach($invoice->payments as $pay)
                    <div class="mt-2 flex items-center justify-between rounded-lg bg-emerald-50 px-3 py-1.5 text-xs dark:bg-emerald-900/10">
                        <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-400">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $pay->payment_reference }}</span>
                            <span class="text-zinc-400">&middot; {{ $pay->payment_date->format('d M Y') }}</span>
                            <span class="capitalize text-zinc-400">{{ str_replace('_', ' ', $pay->payment_method) }}</span>
                        </div>
                        <span class="font-semibold tabular-nums text-emerald-700 dark:text-emerald-400">UGX {{ number_format($pay->amount, 0) }}</span>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Right: Summary + Audit Log --}}
        <div class="space-y-5">

            {{-- Financial Summary --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Financial Summary</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">PO Value</span>
                        <span class="font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->order->total_amount, 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">Total Invoiced</span>
                        <span class="font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->order->totalInvoiced(), 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">Total Paid</span>
                        <span class="font-semibold tabular-nums text-emerald-600 dark:text-emerald-400">UGX {{ number_format($this->order->totalPaid(), 0) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-zinc-100 pt-3 dark:border-zinc-700/60">
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">Outstanding</span>
                        <span class="font-bold tabular-nums text-red-600 dark:text-red-400">UGX {{ number_format($this->order->outstandingBalance(), 0) }}</span>
                    </div>
                </div>
                @if($this->order->expected_at)
                <div class="mt-4 rounded-lg bg-zinc-50 px-3 py-2 text-xs text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400">
                    Expected delivery: {{ $this->order->expected_at->format('d M Y') }}
                </div>
                @endif
            </div>

            {{-- Audit Log --}}
            @if($this->order->auditLogs->isNotEmpty())
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Activity Log</h2>
                <div class="space-y-3">
                    @foreach($this->order->auditLogs->sortByDesc('created_at')->take(10) as $log)
                    <div class="flex gap-3">
                        <div class="mt-1 size-1.5 shrink-0 rounded-full bg-zinc-400 dark:bg-zinc-600"></div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-zinc-700 dark:text-zinc-300">{{ $log->description }}</p>
                            <p class="text-xs text-zinc-400 dark:text-zinc-500">
                                {{ $log->created_at->format('d M Y, H:i') }}
                                @if($log->user) &middot; {{ $log->user->name }} @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Cancellation Details --}}
            @if($this->order->order_status === \App\Enums\OrderStatus::Cancelled && $this->order->cancellation->isNotEmpty())
            @php $cancel = $this->order->cancellation->last(); @endphp
            <div class="rounded-xl border border-red-200 bg-red-50 p-5 dark:border-red-800/30 dark:bg-red-900/10">
                <h2 class="mb-2 text-sm font-semibold text-red-700 dark:text-red-400">Cancellation Details</h2>
                <p class="text-xs text-red-600 dark:text-red-400">{{ $cancel->cancellation_type->label() }}</p>
                <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">{{ $cancel->reason }}</p>
                @if($cancel->cancelled_at)
                    <p class="mt-1 text-xs text-zinc-400">{{ $this->order->cancelled_at?->format('d M Y, H:i') }}</p>
                @endif
            </div>
            @endif
        </div>
    </div>

</div>
