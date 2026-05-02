<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('purchase-orders.show', $purchaseOrder->id) }}" wire:navigate
            class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 shadow-sm hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Add Supplier Invoice</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                Record an invoice against <span class="font-mono font-semibold text-zinc-700 dark:text-zinc-300">{{ $purchaseOrder->po_number }}</span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">

            {{-- Invoice Details --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Invoice Details</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>Invoice Date <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="invoiceDate" type="date" />
                        <flux:error name="invoiceDate" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Due Date</flux:label>
                        <flux:input wire:model="dueDate" type="date" />
                        <flux:error name="dueDate" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Supplier Invoice Ref</flux:label>
                        <flux:input wire:model="supplierRef" placeholder="Supplier's own invoice number" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Notes</flux:label>
                        <flux:input wire:model="notes" placeholder="Optional notes…" />
                    </flux:field>
                </div>
            </div>

            {{-- Line Items --}}
            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Invoice Lines</h2>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-100 bg-zinc-50/60 dark:border-zinc-700/50 dark:bg-zinc-800/40">
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Item</th>
                            <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500">Pending</th>
                            <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500">Qty</th>
                            <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500">Unit Cost</th>
                            <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                        @foreach($lines as $i => $line)
                        <tr wire:key="inv-line-{{ $i }}">
                            <td class="px-5 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $line['item_name'] }}</td>
                            <td class="px-5 py-3 text-right tabular-nums text-zinc-500 dark:text-zinc-400">{{ $line['pending'] }}</td>
                            <td class="px-5 py-3 text-right">
                                <flux:input wire:model.live="lines.{{ $i }}.quantity" type="number" min="0" max="{{ $line['pending'] }}" class="w-20 text-right" />
                            </td>
                            <td class="px-5 py-3 text-right">
                                <flux:input wire:model.live="lines.{{ $i }}.unit_cost" type="text" x-mask:dynamic="$money($input)" class="w-28 text-right" />
                            </td>
                            <td class="px-5 py-3 text-right font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">
                                UGX {{ number_format($line['quantity'] * $line['unit_cost'], 0) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-zinc-200 bg-zinc-50/60 dark:border-zinc-700/60 dark:bg-zinc-800/40">
                            <td colspan="4" class="px-5 py-3 text-right text-sm font-semibold text-zinc-900 dark:text-zinc-100">Invoice Total</td>
                            <td class="px-5 py-3 text-right font-bold tabular-nums text-zinc-900 dark:text-zinc-100">
                                UGX {{ number_format($this->totalAmount, 0) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Right --}}
        <div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900 space-y-4">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Summary</h2>
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500 dark:text-zinc-400">Invoice Total</span>
                    <span class="font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->totalAmount, 0) }}</span>
                </div>
                <flux:button wire:click="save" variant="primary" icon="document-text" class="w-full">
                    Save Invoice
                </flux:button>
                <a href="{{ route('purchase-orders.show', $purchaseOrder->id) }}" wire:navigate>
                    <flux:button variant="ghost" class="w-full mt-1">Cancel</flux:button>
                </a>
            </div>
        </div>
    </div>

</div>
