<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('purchase-orders.index') }}" wire:navigate
            class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 shadow-sm hover:bg-zinc-50 hover:text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
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
                    <flux:field>
                        <flux:label>Supplier <span class="text-red-500">*</span></flux:label>
                        <select wire:model="supplierId"
                            class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                            <option value="">Select a supplier</option>
                            @foreach($this->suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        <flux:error name="supplierId" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Expected Delivery Date</flux:label>
                        <flux:input wire:model="expectedAt" type="date" />
                        <flux:error name="expectedAt" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Notes</flux:label>
                        <flux:textarea wire:model="notes" rows="2" placeholder="Any special instructions…" />
                    </flux:field>
                </div>
            </div>

            {{-- Add Item --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Add Item</h2>
                <div class="flex flex-wrap items-end gap-3">
                    <flux:field class="min-w-0 flex-1">
                        <flux:label>Item <span class="text-red-500">*</span></flux:label>
                        <select wire:model.live="pickerItemId"
                            class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                            <option value="">Select item</option>
                            @foreach($this->inventoryItems as $inv)
                                <option value="{{ $inv->id }}">{{ $inv->name }}</option>
                            @endforeach
                        </select>
                        <flux:error name="pickerItemId" />
                    </flux:field>
                    @if($this->selectedItemVariants->count() > 0)
                        <flux:field class="w-40">
                            <flux:label>Variant</flux:label>
                            <select wire:model.live="pickerVariantId"
                                class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                                <option value="">Any</option>
                                @foreach($this->selectedItemVariants as $variant)
                                    <option value="{{ $variant->id }}">{{ $variant->name }}</option>
                                @endforeach
                            </select>
                        </flux:field>
                    @endif
                    <flux:field class="w-24">
                        <flux:label>Qty</flux:label>
                        <flux:input wire:model="pickerQuantity" type="number" min="1" />
                        <flux:error name="pickerQuantity" />
                    </flux:field>
                    <flux:field class="w-36">
                        <flux:label>Unit Cost (UGX)</flux:label>
                        <flux:input wire:model="pickerUnitCost" type="number" min="0" step="100" placeholder="0" />
                        <flux:error name="pickerUnitCost" />
                    </flux:field>
                    <flux:button wire:click="addLineItem" variant="filled" icon="plus" class="shrink-0">Add</flux:button>
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
                                <div class="min-w-0 flex-1">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $line['item_name'] }}</div>
                                    @if(!empty($line['variant_name']))
                                        <div class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">{{ $line['variant_name'] }}</div>
                                    @endif
                                </div>
                                <div class="hidden w-36 sm:block">
                                    <div class="mb-1 text-xs text-zinc-400 dark:text-zinc-500">Unit cost</div>
                                    <div class="flex items-center rounded-lg border border-zinc-200 bg-zinc-50 px-2.5 py-1 dark:border-zinc-700 dark:bg-zinc-800">
                                        <span class="mr-1 text-xs text-zinc-400">UGX</span>
                                        <input
                                            type="number"
                                            min="0"
                                            step="1"
                                            value="{{ $line['unit_cost'] }}"
                                            wire:change="updateLineCost({{ $index }}, $event.target.value)"
                                            class="w-full bg-transparent text-sm font-medium tabular-nums text-zinc-800 focus:outline-none dark:text-zinc-200"
                                        />
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 rounded-lg border border-zinc-200 bg-zinc-50 p-1 dark:border-zinc-700 dark:bg-zinc-800">
                                    <button type="button" wire:click="updateLineQuantity({{ $index }}, {{ max(1, $line['quantity'] - 1) }})"
                                        class="flex size-6 items-center justify-center rounded text-zinc-500 hover:bg-white hover:text-zinc-700 dark:hover:bg-zinc-700 transition-colors">
                                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                    </button>
                                    <span class="w-8 text-center text-sm font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">{{ $line['quantity'] }}</span>
                                    <button type="button" wire:click="updateLineQuantity({{ $index }}, {{ $line['quantity'] + 1 }})"
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
                    <flux:button wire:click="save('sent')" variant="primary" icon="paper-airplane"
                        :disabled="count($lineItems) === 0" class="w-full">
                        {{ $editingOrderId ? 'Update & Send' : 'Create & Send' }}
                    </flux:button>
                    <flux:button wire:click="save('approved')" variant="filled" icon="check"
                        :disabled="count($lineItems) === 0" class="w-full">
                        Save as Approved
                    </flux:button>
                    <flux:button wire:click="save('draft')" variant="ghost" class="w-full">
                        Save as Draft
                    </flux:button>
                </div>
            </div>

            @if(count($lineItems) === 0)
                <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 p-5 text-center dark:border-zinc-700 dark:bg-zinc-800/40">
                    <svg class="mx-auto mb-2 size-8 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">No items added yet.</p>
                </div>
            @endif
        </div>
    </div>

</div>
