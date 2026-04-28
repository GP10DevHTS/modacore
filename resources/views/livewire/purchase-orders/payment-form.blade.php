<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('purchase-orders.show', $invoice->purchase_order_id) }}" wire:navigate
            class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 shadow-sm hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Record Payment</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                For invoice <span class="font-mono font-semibold text-zinc-700 dark:text-zinc-300">{{ $invoice->invoice_number }}</span>
                &middot; Outstanding: <span class="font-semibold text-red-600">UGX {{ number_format($invoice->outstandingBalance(), 0) }}</span>
            </p>
        </div>
    </div>

    <div class="mx-auto max-w-lg">
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900 space-y-5">

            <flux:field>
                <flux:label>Amount (UGX) <span class="text-red-500">*</span></flux:label>
                <flux:input wire:model="amount" type="number" min="0.01" step="100" placeholder="0" />
                <flux:error name="amount" />
            </flux:field>

            <flux:field>
                <flux:label>Payment Date <span class="text-red-500">*</span></flux:label>
                <flux:input wire:model="paymentDate" type="date" />
                <flux:error name="paymentDate" />
            </flux:field>

            <flux:field>
                <flux:label>Payment Method <span class="text-red-500">*</span></flux:label>
                <select wire:model="paymentMethod"
                    class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                    @foreach($this->paymentMethods as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
                <flux:error name="paymentMethod" />
            </flux:field>

            <flux:field>
                <flux:label>External Reference</flux:label>
                <flux:input wire:model="externalReference" placeholder="Bank ref, MM transaction ID, cheque no…" />
                <flux:error name="externalReference" />
            </flux:field>

            <flux:field>
                <flux:label>Notes</flux:label>
                <flux:textarea wire:model="notes" rows="2" placeholder="Optional payment notes…" />
            </flux:field>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('purchase-orders.show', $invoice->purchase_order_id) }}" wire:navigate class="flex-1">
                    <flux:button variant="ghost" class="w-full">Cancel</flux:button>
                </a>
                <flux:button wire:click="save" variant="primary" icon="banknotes" class="flex-1">
                    Record Payment
                </flux:button>
            </div>
        </div>
    </div>

</div>
