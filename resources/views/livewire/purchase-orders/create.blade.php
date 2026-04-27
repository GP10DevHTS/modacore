<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('purchase-orders.index') }}" wire:navigate
            class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 shadow-sm hover:bg-zinc-50 hover:text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                {{ $editingOrderId ? 'Edit Purchase Order' : 'New Purchase Order' }}
            </h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                {{ $editingOrderId ? 'Update order details and items.' : 'Create a new supplier purchase order.' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Left: Form --}}
        <div class="space-y-5 lg:col-span-2">

            {{-- Order Details --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Order Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Supplier <span class="text-red-500">*</span></label>
                        <flux:select wire:model="supplierId" placeholder="Select a supplier">
                            @foreach($this->suppliers as $supplier)
                                <flux:select.option value="{{ $supplier->id }}">{{ $supplier->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="supplierId" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Expected Delivery Date</label>
                        <flux:input wire:model="expectedAt" type="date" />
                        <flux:error name="expectedAt" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Notes</label>
                        <flux:textarea wire:model="notes" rows="2" placeholder="Any special instructions…" />
                    </div>
                </div>
            </div>

            {{-- Add Item --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Add Item</h2>
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-0 flex-1">
                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Item <span class="text-red-500">*</span></label>
                        <flux:select wire:model.live="pickerItemId" placeholder="Select item">
                            @foreach($this->inventoryItems as $item)
                                <flux:select.option value="{{ $item->id }}">{{ $item->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="pickerItemId" />
                    </div>
                    <div class="w-24">
                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Qty</label>
                        <flux:input wire:model="pickerQuantity" type="number" min="1" />
                        <flux:error name="pickerQuantity" />
                    </div>
                    <div class="w-36">
                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Unit Cost (UGX)</label>
                        <flux:input wire:model="pickerUnitCost" type="number" min="0" step="100" placeholder="0" />
                        <flux:error name="pickerUnitCost" />
                    </div>
                    <button wire:click="addLineItem"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 transition-colors shrink-0">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add
                    </button>
                </div>
            </div>

            {{-- Line Items --}}
            @if(count($lineItems) > 0)
                <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                        <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Order Items</h2>
                        <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ count($lineItems) }} {{ Str::plural('item', count($lineItems)) }}</span>
                    </div>
                    <div class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                        @foreach($lineItems as $index => $line)
                            <div class="flex items-center gap-4 px-5 py-3.5" wire:key="line-{{ $index }}">
                                <div class="min-w-0 flex-1 font-medium text-zinc-900 dark:text-zinc-100">{{ $line['item_name'] }}</div>
                                <div class="hidden w-32 text-right sm:block">
                                    <div class="text-xs text-zinc-400 dark:text-zinc-500">Unit cost</div>
                                    <div class="font-medium tabular-nums text-zinc-700 dark:text-zinc-300">UGX {{ number_format($line['unit_cost'], 0) }}</div>
                                </div>
                                <div class="flex items-center gap-1 rounded-lg border border-zinc-200 bg-zinc-50 p-1 dark:border-zinc-700 dark:bg-zinc-800">
                                    <button type="button"
                                        wire:click="updateLineQuantity({{ $index }}, {{ max(1, $line['quantity'] - 1) }})"
                                        class="flex size-6 items-center justify-center rounded text-zinc-500 hover:bg-white hover:text-zinc-700 dark:hover:bg-zinc-700 transition-colors">
                                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                    </button>
                                    <span class="w-8 text-center text-sm font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">{{ $line['quantity'] }}</span>
                                    <button type="button"
                                        wire:click="updateLineQuantity({{ $index }}, {{ $line['quantity'] + 1 }})"
                                        class="flex size-6 items-center justify-center rounded text-zinc-500 hover:bg-white hover:text-zinc-700 dark:hover:bg-zinc-700 transition-colors">
                                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </div>
                                <div class="w-28 text-right font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">
                                    UGX {{ number_format($line['subtotal'], 0) }}
                                </div>
                                <button wire:click="removeLineItem({{ $index }})"
                                    class="rounded-lg p-1.5 text-zinc-300 hover:bg-red-50 hover:text-red-500 dark:text-zinc-600 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex items-center justify-end gap-2 border-t border-zinc-100 bg-zinc-50/60 px-5 py-3 dark:border-zinc-700/60 dark:bg-zinc-800/40">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Total</span>
                        <span class="text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->totalAmount, 0) }}</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right: Summary --}}
        <div class="space-y-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Summary</h2>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between text-zinc-500 dark:text-zinc-400">
                        <span>Items added</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ count($lineItems) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-zinc-100 pt-2.5 dark:border-zinc-700/60">
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">Total</span>
                        <span class="text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->totalAmount, 0) }}</span>
                    </div>
                </div>
                <div class="mt-5 space-y-2.5">
                    <button wire:click="save('sent')"
                        @if(count($lineItems) === 0) disabled @endif
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-black hover:bg-amber-400 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        {{ $editingOrderId ? 'Update & Send' : 'Create & Send' }}
                    </button>
                    <button wire:click="save('draft')"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-colors">
                        Save as Draft
                    </button>
                </div>
            </div>

            @if(count($lineItems) === 0)
                <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 p-5 text-center dark:border-zinc-700 dark:bg-zinc-800/40">
                    <svg class="mx-auto mb-2 size-8 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">No items added yet.</p>
                </div>
            @endif
        </div>
    </div>

</div>
